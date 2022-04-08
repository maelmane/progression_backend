<?php
/*
This file is part of Progression.

Progression is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Progression is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Progression.  If not, see <https://www.gnu.org/licenses/>.
*/

use progression\TestCase;

use progression\dao\DAOFactory;
use progression\http\contrôleur\GénérateurDeToken;
use progression\domaine\entité\User;
use Illuminate\Auth\GenericUser;

final class AuthServiceProviderCtlTests extends TestCase
{
	public $utilisateurLambda;

	public function setUp(): void
	{
		parent::setUp();

		$this->utilisateurLambda = new GenericUser(["username" => "utilisateur_lambda", "rôle" => User::ROLE_NORMAL]);

		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("utilisateur_lambda")
			->andReturn(new User("utilisateur_lambda"));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("autre_utilisateur")
			->andReturn(new User("autre_utilisateur"));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("utilisateur_innocent")
			->andReturn(new User("utilisateur_innocent"));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("utilisateur_malveillant")
			->andReturn(new User("utilisateur_malveillant"));

		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_lorsque_le_token_expire_dans_1_seconde_on_obtient_un_code_200()
	{
		$ressources = '{
			"ressources": {
			  "url": "*",
			  "method": "GET"
			}
		  }';
		$expiration = time() + 1;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $expiration, $ressources);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(200, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_lorsque_la_date_dexpiration_du_token_est_0_on_obtient_un_code_200()
	{
		$ressources = '{
			"ressources": {
			  "url": "*",
			  "method": "GET"
			}
		  }';
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $expiration, $ressources);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(200, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_lorsque_la_date_dexpiration_est_échue_depuis_1_seconde_on_obtient_une_erreur_401()
	{
		$ressources = '{
			"ressources": {
			  "url": "*",
			  "method": "GET"
			}
		  }';
		$expiration = time() - 1;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $expiration, $ressources);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(401, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_lorsquon_effectue_un_get_alors_quil_ne_peut_faire_quun_post_on_obtient_une_erreur_403()
	{
		$ressources = '{
			"ressources": {
			  "url": "*",
			  "method": "POST"
			}
		  }';
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $expiration, $ressources);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->actingAs($this->utilisateurLambda)->call($method, $route, [], [], [], $headers);

		$this->assertEquals(403, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_qui_peut_effectuer_toutes_les_méthodes_de_requête_avec_létoile_lorsquon_effectue_un_get_on_obtient_un_code_200()
	{
		$ressources = '{
			"ressources": {
			  "url": "*",
			  "method": "*"
			}
		  }';
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $expiration, $ressources);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->actingAs($this->utilisateurLambda)->call($method, $route, [], [], [], $headers);

		$this->assertEquals(200, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_qui_donne_access_a_cet_utilisateur_lorsquon_effectue_une_requête_dacces_à_ses_ressources_on_obtient_un_code_200()
	{
		$ressources = '{
			"ressources": {
			  "url": "user/utilisateur_lambda",
			  "method": "GET"
			}
		  }';
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $expiration, $ressources);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->actingAs($this->utilisateurLambda)->call($method, $route, [], [], [], $headers);

		$this->assertEquals(200, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_pour_un_autre_utilisateur_lorsquon_effectue_une_requete_pour_accéder_aux_ressources_dun_autre_utilsateur_on_obtient_une_erreur_403()
	{
		$ressources = '{
			"ressources": {
			  "url": "user/autre_utilisateur",
			  "method": "GET"
			}
		  }';
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $expiration, $ressources);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->actingAs($this->utilisateurLambda)->call($method, $route, [], [], [], $headers);

		$this->assertEquals(403, $résultatObtenu->status());
	}

	public function test_étant_donné_un_utilisateur_malveillant_lorsquon_fabrique_un_token_pour_accéder_aux_ressources_dun_utilisateur_innocent_on_obtient_une_erreur_403()
	{
		$utilisateurMalveillant = new GenericUser([
			"username" => "utilisateur_malveillant",
			"rôle" => User::ROLE_NORMAL,
		]);
		$ressourcesUtilisateurMalveillant = '{
			"ressources": {
			  "url": "*",
			  "method": "*"
			}
		  }';

		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token(
			"utilisateur_malveillant",
			$expiration,
			$ressourcesUtilisateurMalveillant,
		);
		$method = "POST";
		$route = "/token/utilisateur_malveillant";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$ressourcesUtilisateurInnocent = '{
			"ressources": {
			  "url": "user/utilisateur_innocent",
			  "method": "GET"
			}
		  }';

		$responseTokenCtl = $this->actingAs($utilisateurMalveillant)->call(
			$method,
			$route,
			["ressources" => $ressourcesUtilisateurInnocent],
			[],
			[],
			$headers,
		);

		$tokenJson = json_decode($responseTokenCtl->getContent(), false);
		$token = $tokenJson->Token;
		$method = "GET";
		$route = "/user/utilisateur_innocent";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $responseTokenCtl = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(403, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_avec_étoile_dans_le_path_lorsquon_effectue_une_requête_pour_les_ressources_après_après_létoile_on_obtient_un_code_200()
	{
		$ressources = '{
			"ressources": {
			  "url": "user/*",
			  "method": "GET"
			}
		  }';
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $expiration, $ressources);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(200, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_avec_étoile_dans_le_path_lorsquon_effectue_une_requête_a_une_ressource_avant_létoile_on_obtient_une_erreur_403()
	{
		$ressources = '{
			"ressources": {
			  "url": "avancement/*",
			  "method": "GET"
			}
		  }';
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $expiration, $ressources);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(403, $résultatObtenu->status());
	}
}
