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
use progression\http\contrôleur\NotImplementedCtl;
use Firebase\JWT\JWT;

final class AuthServiceProviderTests extends TestCase
{
	public $utilisateurLambda;
	public $tokenUtilisateurLambda;

	public function setUp(): void
	{
		parent::setUp();

		$this->utilisateurLambda = new GenericUser(["username" => "utilisateur_lambda", "rôle" => User::ROLE_NORMAL]);
		$this->tokenUtilisateurLambda = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda");

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
		$mockUserDAO->shouldReceive("get_user")->andReturn(null);

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
		$expiration = time() + 1;
		$tokenUtilisateurLambda = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $expiration);
		$this->call(
			"GET",
			"/user/utilisateur_lambda",
			[],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurLambda],
		);

		$this->assertResponseStatus(200);
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_lorsque_la_date_dexpiration_du_token_est_0_on_obtient_un_code_200()
	{
		$expiration = 0;
		$tokenUtilisateurLambda = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $expiration);
		$this->call(
			"GET",
			"/user/utilisateur_lambda",
			[],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurLambda],
		);

		$this->assertResponseStatus(200);
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_lorsque_la_date_dexpiration_est_échue_depuis_1_seconde_on_obtient_une_erreur_401()
	{
		$expiration = time() - 1;
		$tokenUtilisateurLambda = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $expiration);
		$this->call(
			"GET",
			"/user/utilisateur_lambda",
			[],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurLambda],
		);

		$this->assertResponseStatus(401);
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_inexistant_lorsque_lorsquon_tente_dacceder_à_une_ressource_on_obtient_une_erreur_401()
	{
		$tokenUtilisateurLambda = GénérateurDeToken::get_instance()->générer_token("utilisateur_inexistant");
		$this->call(
			"GET",
			"/user/utilisateur_lambda",
			[],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurLambda],
		);

		$this->assertResponseStatus(401);
	}

	public function test_étant_donné_un_token_avec_un_url_lorsquon_effectue_une_requête_a_un_url_valide_on_obtient_un_code_200()
	{
		$ressources = json_encode([["url" => "/^user\/utilisateur_lambda$/", "method" => "/get/i"]]);
		$tokenUtilisateurLambda = GénérateurDeToken::get_instance()->générer_token(
			"utilisateur_lambda",
			0,
			$ressources,
		);
		$résultatObtenu = $this->call(
			"GET",
			"/user/utilisateur_lambda",
			[],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurLambda],
		);

		$this->assertResponseStatus(200);
	}

	public function test_étant_donné_un_token_avec_un_url_lorsquon_effectue_une_requête_a_un_url_valide_mais_non_autorisée_on_obtient_un_code_403()
	{
		$ressources = json_encode([["url" => "/^user\/utilisateur_lambda$/", "method" => "/post/i"]]);
		$tokenUtilisateurLambda = GénérateurDeToken::get_instance()->générer_token(
			"utilisateur_lambda",
			0,
			$ressources,
		);
		$résultatObtenu = $this->call(
			"POST",
			"/user/utilisateur_lambda/cles",
			[],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurLambda],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_lambda_lorsquon_effectue_une_requête_pour_accéder_aux_ressources_dun_autre_utilisateur_on_obtient_une_erreur_403()
	{
		$résultatObtenu = $this->call(
			"GET",
			"/user/autre_utilisateur",
			[],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurLambda],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_token_ressource_qui_contient_différents_url_lorsquon_effectue_une_requête_à_une_ressource_autorisée_on_obtient_200()
	{
		$ressources = json_encode([
			["url" => "/^autre\/ressource_test$/", "method" => "/get/i"],
			["url" => "/^user\/autre_utilisateur$/", "method" => "/get/i"],
			["url" => "/^ressource\/autre_test$/", "method" => "/.*/"],
		]);
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

		$this->call(
			"GET",
			"user/autre_utilisateur",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurLambda],
		);

		$this->assertResponseStatus(200);
	}

	public function test_étant_donné_un_token_ressource_qui_contient_différents_url_lorsquon_effectue_une_requête_à_une_ressource_non_autorisée_on_obtient_403()
	{
		$ressources = json_encode([
			["url" => "/^user\/autre_utilisateur\/avancements$/", "method" => "/get/i"],
			["url" => "/^user\/autre_utilisateur$/", "method" => "/get/i"],
			["url" => "/^user\/autre_utilisateur\/relationships\/avancement$/", "method" => "/post/i"],
		]);
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

		$this->call(
			"POST",
			"/user/autre_utilisateur/cles",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurLambda],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_utilisateur_malveillant_lorsquon_fabrique_un_token_pour_accéder_aux_ressources_dun_utilisateur_innocent_on_obtient_une_erreur_403()
	{
		$tokenUtilisateurMalveillant = GénérateurDeToken::get_instance()->générer_token("utilisateur_malveillant");

		$ressourcesUtilisateurInnocent = json_encode([
			["url" => "/^user\/utilisateur_innocent$/", "method" => "/post/i"],
		]);

		$responseTokenCtl = $this->call(
			"POST",
			"/token/utilisateur_malveillant",
			["ressources" => $ressourcesUtilisateurInnocent],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurMalveillant],
		);

		$tokenJson = json_decode($responseTokenCtl->getContent(), false);
		$tokenUtilisateurMalveillant = $tokenJson->Token;
		$this->call(
			"GET",
			"/user/utilisateur_innocent",
			["ressources" => $tokenUtilisateurMalveillant],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurMalveillant],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_token_ressource_qui_contient_seulement_une_méthode_POST_lorsquon_effectue_une_requête_avec_GET_on_obtient_un_code_403()
	{
		$ressources = json_encode([["url" => "/.*/", "", "method" => "/post/i"]]);
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

		$this->call(
			"GET",
			"/user/autre_utilisateur",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurLambda],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_token_expiré_et_un_token_ressource_valide_lorsquon_effectue_une_requête_on_obtient_401()
	{
		$tokenUtilisateurLambda = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", time() - 1);
		$ressources = json_encode([["url" => "/^user\/autre_utilisateur$/", "method" => "/get/i"]]);
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

		$this->call(
			"GET",
			"/user/autre_utilisateur",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurLambda],
		);

		$this->assertResponseStatus(401);
	}

	public function test_étant_donné_un_token_valide_et_un_token_ressource_expiré_lorsquon_effectue_une_requête_pour_ses_propres_ressources_on_obtient_200()
	{
		$tokenUtilisateurLambda = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda");
		$ressources = json_encode([["url" => "/^user\/autre_utilisateur$/", "method" => "/get/i"]]);
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token(
			"autre_utilisateur",
			time() - 1,
			$ressources,
		);

		$this->call(
			"GET",
			"/user/utilisateur_lambda",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurLambda],
		);

		$this->assertResponseStatus(200);
	}

	public function test_étant_donné_un_token_avec_une_mauvaise_signature_et_un_token_ressource_valide_lorsquon_effectue_une_requête_on_obtient_401()
	{
		$ressources = json_encode([["url" => "/^user\/autre_utilisateur$/", "method" => "/get/i"]]);
		$payload = [
			"username" => "utilisateur_lambda",
			"current" => time(),
			"expired" => 0,
			"ressources" => $ressources,
			"version" => 1,
		];

		$tokenUtilisateurLambda = JWT::encode($payload, "mauvais_secret", "HS256");
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

		$this->call(
			"GET",
			"/user/autre_utilisateur",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurLambda],
		);

		$this->assertResponseStatus(401);
	}

	public function test_étant_donné_un_token_valide_et_un_token_ressource_expiré_lorsquon_requiert_une_ressource_dun_autre_utilisateur_on_obtient_403()
	{
		$ressources = json_encode([["url" => "/^user\/autre_utilisateur$/", "method" => "/get/i"]]);
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token(
			"autre_utilisateur",
			time() - 1,
			$ressources,
		);

		$this->call(
			"GET",
			"/user/autre_utilisateur",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurLambda],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_token_valide_et_un_token_ressource_avec_une_mauvaise_signature_lorsquon_requiert_une_ressource_dun_autre_utilisateur_on_obtient_403()
	{
		$ressources = json_encode([["url" => "/^user\/autre_utilisateur$/", "method" => "/get/i"]]);
		$payload = [
			"username" => "autre_utilisateur",
			"current" => time(),
			"expired" => 0,
			"ressources" => $ressources,
			"version" => 1,
		];
		$tokenRessource = JWT::encode($payload, "mauvais_secret", "HS256");

		$this->call(
			"GET",
			"/user/autre_utilisateur",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurLambda],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_token_valide_et_un_token_ressource_mal_formaté_sans_ressources_lorsquon_requiert_une_ressource_dun_autre_utilisateur_on_obtient_403()
	{
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, null);

		$this->call(
			"GET",
			"/user/autre_utilisateur",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurLambda],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_token_valide_et_un_token_ressource_mal_formaté_sans_url_lorsquon_requiert_une_ressource_dun_autre_utilisateur_on_obtient_403()
	{
		$ressources = json_encode([["url" => "", "method" => "/.*/"]]);
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

		$this->call(
			"GET",
			"/user/autre_utilisateur",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurLambda],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_token_valide_et_un_token_ressource_mal_formaté_sans_méthode_lorsquon_requiert_une_ressource_dun_autre_utilisateur_on_obtient_403()
	{
		$ressources = json_encode([["url" => "/.*/", "method" => ""]]);
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

		$this->call(
			"GET",
			"/user/autre_utilisateur",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurLambda],
		);

		$this->assertResponseStatus(403);
	}
}
