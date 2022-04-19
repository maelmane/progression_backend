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
use Firebase\JWT\JWT;

final class AuthServiceProviderCtlTests extends TestCase
{
	public $utilisateurLambda;
	public $token;

	public function setUp(): void
	{
		parent::setUp();

		$this->utilisateurLambda = new GenericUser(["username" => "utilisateur_lambda", "rôle" => User::ROLE_NORMAL]);
		$this->token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda");

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
		$mockUserDAO
			->shouldReceive("get_user")
			->with("utilisateur_inexistant")
			->andReturn(null);

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
		$ressources = json_encode(["ressources" => ["url" => ["*"], "method" => "*"]]);
		$expiration = time() + 1;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $expiration, $ressources);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->actingAs($this->utilisateurLambda)->call($method, $route, [], [], [], $headers);

		$this->assertEquals(200, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_lorsque_la_date_dexpiration_du_token_est_0_on_obtient_un_code_200()
	{
		$ressources = json_encode(["ressources" => ["url" => ["*"], "method" => "*"]]);
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $expiration, $ressources);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->actingAs($this->utilisateurLambda)->call($method, $route, [], [], [], $headers);

		$this->assertEquals(200, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_lorsque_la_date_dexpiration_est_échue_depuis_1_seconde_on_obtient_une_erreur_403()
	{
		$ressources = json_encode(["ressources" => ["url" => ["*"], "method" => "*"]]);
		$expiration = time() - 1;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $expiration, $ressources);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->actingAs($this->utilisateurLambda)->call($method, $route, [], [], [], $headers);

		$this->assertEquals(403, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_inexistant_lorsque_lorsquon_tente_dacceder_a_une_ressource_on_obtient_une_erreur_403()
	{
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_inexistant");
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(401, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_qui_peut_effectuer_toutes_les_méthodes_de_requête_avec_létoile_lorsquon_effectue_un_get_on_obtient_un_code_200()
	{
		$ressources = json_encode(["ressources" => ["url" => ["*"], "method" => "*"]]);
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $expiration, $ressources);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->actingAs($this->utilisateurLambda)->call($method, $route, [], [], [], $headers);

		$this->assertEquals(200, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_qui_donne_access_à_cet_utilisateur_lorsquon_effectue_une_requête_dacces_à_ses_ressources_on_obtient_un_code_200()
	{
		$ressources = json_encode(["ressources" => ["url" => ["user/utilisateur_lambda"], "method" => "GET"]]);
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $expiration, $ressources);
		$method = "GET";
		$route = "/user/utilisateur_lambda";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];

		$résultatObtenu = $this->actingAs($this->utilisateurLambda)->call($method, $route, [], [], [], $headers);

		$this->assertEquals(200, $résultatObtenu->status());
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_lorsquon_effectue_une_requête_pour_accéder_aux_ressources_dun_autre_utilisateur_on_obtient_une_erreur_403()
	{
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda");
		$method = "GET";
		$route = "/user/utilisateur_innocent";
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
		$ressourcesUtilisateurMalveillant = json_encode(["ressources" => ["url" => ["*"], "method" => "*"]]);
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token(
			"utilisateur_malveillant",
			$expiration,
			$ressourcesUtilisateurMalveillant,
		);

		$method = "POST";
		$route = "/token/utilisateur_malveillant";
		$headers = ["HTTP_Authorization" => "Bearer " . $token];
		$ressourcesUtilisateurInnocent = json_encode([
			"ressources" => ["url" => ["user/utilisateur_innocent"], "method" => "GET"],
		]);

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

		$résultatObtenu = $this->call($method, $route, [], [], [], $headers);

		$this->assertEquals(403, $résultatObtenu->status());
	}

	public function test_étant_donné_le_token_ressource_dun_autre_utilisateur_lorsque_lutilisateur_lambda_utilise_ce_token_comme_token_ressource_on_obtient_un_code_200()
	{
		$expiration = 0;
		$ressources = json_encode([
			"ressources" => ["url" => ["user/autre_utilisateur", "avancement/autre_utilisateur/*"], "method" => "GET"],
		]);
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token(
			"autre_utilisateur",
			$expiration,
			$ressources,
		);
		$method = "GET";
		$route = "user/autre_utilisateur";
		$headers = ["HTTP_Authorization" => "Bearer " . $this->token];

		$résultatObtenu = $this->actingAs($this->utilisateurLambda)->call(
			$method,
			$route,
			["tkres" => $tokenRessource],
			[],
			[],
			$headers,
		);

		$this->assertEquals(200, $résultatObtenu->status());
	}

	public function test_étant_donné_le_token_ressource_qui_donne_acces_a_toutes_les_ressources_dun_contrôleur_lorsque_lutilisateur_lambda_utilise_ce_token_comme_token_ressource_on_obtient_un_code_200()
	{
		$expiration = 0;
		$ressources = json_encode([
			"ressources" => [
				"url" => ["user/*", "avancement/autre_utilisateur/*", "autre_url/autre_url"],
				"method" => "GET",
			],
		]);
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token(
			"autre_utilisateur",
			$expiration,
			$ressources,
		);
		$method = "GET";
		$route = "user/autre_utilisateur";
		$headers = ["HTTP_Authorization" => "Bearer " . $this->token];

		$résultatObtenu = $this->actingAs($this->utilisateurLambda)->call(
			$method,
			$route,
			["tkres" => $tokenRessource],
			[],
			[],
			$headers,
		);

		$this->assertEquals(200, $résultatObtenu->status());
	}

	public function test_étant_donné_le_token_ressource_dun_autre_utilisateur_qui_ne_contient_pas_les_bonnes_ressources_lorsque_lutilisateur_lambda_utilise_ce_token_comme_token_ressource_on_obtient_un_code_403()
	{
		$expiration = 0;
		$ressources = json_encode(["ressources" => ["url" => ["mauvais/url/*", "url/mauvais/*"], "method" => "GET"]]);
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token(
			"autre_utilisateur",
			$expiration,
			$ressources,
		);
		$method = "GET";
		$route = "/user/autre_utilisateur";
		$headers = ["HTTP_Authorization" => "Bearer " . $this->token];

		$résultatObtenu = $this->actingAs($this->utilisateurLambda)->call(
			$method,
			$route,
			["tkres" => $tokenRessource],
			[],
			[],
			$headers,
		);

		$this->assertEquals(403, $résultatObtenu->status());
	}
}
