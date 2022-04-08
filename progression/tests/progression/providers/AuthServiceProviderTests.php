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
use progression\domaine\entité\{User};

final class AuthServiceProviderCtlTests extends TestCase
{
	public function setUp(): void
	{
		//UserDAO
		parent::setUp();

		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("utilisateur_lambda")
			->andReturn(new User("utilisateur_lambda"));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("autre_utilisateur")
			->andReturn(new User("autre_utilisateur"));

		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_qui_expire_dans_1_seconde_lautorisation_daccès_est_donnée_par_le_système_avec_un_code_200()
	{
		$ressources = '{
			"ressources": {
			  "url": "*",
			  "method": "GET"
			}
		  }';
		$expiration = time() + 1;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $ressources, $expiration);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(200, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_dont_la_date_dexpiration_est_0_lautorisation_daccès_est_donnée_avec_un_code_200()
	{
		$ressources = '{
			"ressources": {
			  "url": "*",
			  "method": "GET"
			}
		  }';
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $ressources, $expiration);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(200, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_dont_la_date_dexpiration_est_échue_depuis_1_seconde_lautorisation_daccès_est_refusée_avec_un_code_401()
	{
		$ressources = '{
			"ressources": {
			  "url": "*",
			  "method": "GET"
			}
		  }';
		$expiration = time() - 1;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $ressources, $expiration);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(401, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_qui_essaie_deffectuer_un_get_alors_quil_ne_peut_faire_quun_post_lautorisation_daccès_est_refusée_avec_un_code_403()
	{
		$ressources = '{
			"ressources": {
			  "url": "*",
			  "method": "POST"
			}
		  }';
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $ressources, $expiration);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(403, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_qui_essaie_deffectuer_un_get_et_qui_peut_effectuer_toutes_les_méthodes_de_requête_avec_létoile_lacces_est_autorisé()
	{
		$ressources = '{
			"ressources": {
			  "url": "*",
			  "method": "*"
			}
		  }';
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $ressources, $expiration);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(200, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_qui_donne_access_a_ce_user_lautorisation_est_donnée_avec_un_code_200()
	{
		$ressources = '{
			"ressources": {
			  "url": "user/utilisateur_lambda",
			  "method": "GET"
			}
		  }';
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $ressources, $expiration);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(200, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_pour_un_autre_utilisateur_lacces_est_refuse_avec_le_code_403_au_detenteur_de_ce_token_sil_essaie_de_faire_une_requête_à_une_ressource_non_inclue_dans_sont_token()
	{
		$ressources = '{
			"ressources": {
			  "url": "user/autre_utilisateur",
			  "method": "GET"
			}
		  }';
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $ressources, $expiration);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(403, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_avec_étoile_dans_le_path_lacces_est_autorisé_avec_le_code_200_au_detenteur_de_ce_token_sil_essaie_de_faire_une_requête_à_une_ressource_située_après_létoile()
	{
		$ressources = '{
			"ressources": {
			  "url": "user/*",
			  "method": "GET"
			}
		  }';
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $ressources, $expiration);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(200, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_avec_étoile_dans_le_path_lacces_est_refusé_avec_le_code_403_au_detenteur_de_ce_token_sil_essaie_de_faire_une_requête_à_une_non_autorisée_située_avant_létoile()
	{
		$ressources = '{
			"ressources": {
			  "url": "avancement/*",
			  "method": "GET"
			}
		  }';
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $ressources, $expiration);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(403, $résultatObtenu->status());
	}
}
