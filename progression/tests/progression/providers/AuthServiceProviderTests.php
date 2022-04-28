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
		$expiration = time() + 1;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $expiration);
		$this->call("GET", "/user/utilisateur_lambda", [], [], [], ["HTTP_Authorization" => "Bearer " . $token]);

		$this->assertResponseStatus(200);
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_lorsque_la_date_dexpiration_du_token_est_0_on_obtient_un_code_200()
	{
		$expiration = 0;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $expiration);
		$this->call("GET", "/user/utilisateur_lambda", [], [], [], ["HTTP_Authorization" => "Bearer " . $token]);

		$this->assertResponseStatus(200);
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_lorsque_la_date_dexpiration_est_échue_depuis_1_seconde_on_obtient_une_erreur_401()
	{
		$expiration = time() - 1;
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", $expiration);
		$this->call("GET", "/user/utilisateur_lambda", [], [], [], ["HTTP_Authorization" => "Bearer " . $token]);

		$this->assertResponseStatus(401);
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_inexistant_lorsque_lorsquon_tente_dacceder_à_une_ressource_on_obtient_une_erreur_401()
	{
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_inexistant");
		$this->call("GET", "/user/utilisateur_lambda", [], [], [], ["HTTP_Authorization" => "Bearer " . $token]);

		$this->assertResponseStatus(401);
	}

	public function test_étant_donné_un_token_avec_un_url_lorsquon_effectue_une_requête_a_un_url_valide_on_obtient_un_code_200()
	{
		$ressources = json_encode([["url" => "/(user\/utilisateur_lambda)/", "method" => "GET"]]);
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", 0, $ressources);
		$résultatObtenu = $this->call(
			"GET",
			"/user/utilisateur_lambda",
			[],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $token],
		);

		$this->assertResponseStatus(200);
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_lambda_lorsquon_effectue_une_requête_pour_accéder_aux_ressources_dun_utilisateur_innocent_on_obtient_une_erreur_403()
	{
		$résultatObtenu = $this->call(
			"GET",
			"/user/utilisateur_innocent",
			[],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->token],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_token_ressource_qui_contient_différents_url_lorsquon_effectue_une_requête_à_une_ressource_autorisée_on_obtient_200()
	{
		$ressources = json_encode([
			["url" => "/(autre\/ressource_test)/", "method" => "GET"],
			["url" => "/(user\/autre_utilisateur)/", "method" => "GET"],
			["url" => "/(ressource\/autre_test)/", "method" => "*"],
		]);
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

		$this->call(
			"GET",
			"user/autre_utilisateur",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->token],
		);

		$this->assertResponseStatus(200);
	}

	public function test_étant_donné_un_token_ressource_qui_contient_différents_url_lorsquon_effectue_une_requête_à_une_ressource_non_autorisée_on_obtient_403()
	{
		$ressources = json_encode([
			["url" => "/(mauvais\/url\/mauvais)/", "method" => "GET"],
			["url" => "/(url\/mauvais\/url)/", "method" => "GET"],
			["url" => "/(autre\/mauvais\/url)/", "method" => "POST"],
		]);
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

		$this->call(
			"GET",
			"/user/autre_utilisateur",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->token],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_utilisateur_malveillant_lorsquon_fabrique_un_token_pour_accéder_aux_ressources_dun_utilisateur_innocent_on_obtient_une_erreur_403()
	{
		$ressourcesUtilisateurMalveillant = json_encode([["url" => "/^.*$/", "method" => "*"]]);
		$token = GénérateurDeToken::get_instance()->générer_token(
			"utilisateur_malveillant",
			0,
			$ressourcesUtilisateurMalveillant,
		);

		$ressourcesUtilisateurInnocent = json_encode([["url" => "/(user\/utilisateur_innocent)/", "method" => "GET"]]);

		$responseTokenCtl = $this->call(
			"POST",
			"/token/utilisateur_malveillant",
			["ressources" => $ressourcesUtilisateurInnocent],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $token],
		);

		$tokenJson = json_decode($responseTokenCtl->getContent(), false);
		$token = $tokenJson->Token;
		$this->call("GET", "/user/utilisateur_innocent", [], [], [], ["HTTP_Authorization" => "Bearer " . $token]);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_token_ressource_dun_autre_utilisateur_lorsque_lutilisateur_lambda_utilise_ce_token_comme_token_ressource_on_obtient_un_code_200()
	{
		$ressources = json_encode([["url" => "/(user\/autre_utilisateur)/", "method" => "GET"]]);
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

		$this->call(
			"GET",
			"user/autre_utilisateur",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->token],
		);

		$this->assertResponseStatus(200);
	}

	public function test_étant_donné_un_token_ressource_dun_utilisateur_malveillant_lorsquon_utilise_ce_token_pour_accéder_à_utilisateur_lambda_on_obtient_403()
	{
		$ressources = json_encode([["url" => "/(user\/utilisateur_malveillant)/", "method" => "GET"]]);
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token("utilisateur_malveillant", 0, $ressources);

		$this->call(
			"GET",
			"user/utilisateur_lambda",
			[],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenRessource],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_token_ressource_qui_contient_seulement_une_méthode_POST_lorsquon_effectue_une_requête_avec_GET_on_obtient_un_code_403()
	{
		$ressources = json_encode([["url" => "/^.*$/", "", "method" => "POST"]]);
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

		$this->call(
			"GET",
			"/user/autre_utilisateur",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->token],
		);

		$this->assertResponseStatus(403);
	}

	// public function test_étant_donné_un_token_ressource_qui_a_une_étoile_à_lintérieur_de_lurl_lorsque_quon_effectue_une_requête_pour_une_ressource_autoriée_on_obtient_501()
	// {
	// 	$ressources = json_encode([["url" => "user\/.[^/]$\/avancements", "method" => "GET"]]);
	// 	$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

	// 	$this->call(
	// 		"GET",
	// 		"/user/autre_utilisateur/avancements",
	// 		["tkres" => $tokenRessource],
	// 		[],
	// 		[],
	// 		["HTTP_Authorization" => "Bearer " . $this->token],
	// 	);
	// 	//Utilisé avec 501 parce que c'est uniquement ce que retourne le contrôleur utilisé pour ce test. 501 veut dire que le contrôleur a été atteint après que la requête ait été jugée valide par le Middleware.
	// 	$this->assertResponseStatus(501);
	// }

	// public function test_étant_donné_un_token_ressources_qui_a_une_étoile_à_lintérieur_de_lurl_lorsquon_effectue_une_requête_pour_une_ressource_non_autorisée_on_obtient_403()
	// {
	// 	$ressources = json_encode([["url" => "user\/.[^/]$\/avancements", "method" => "GET"]]);
	// 	$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

	// 	$this->call(
	// 		"GET",
	// 		"/user/autre_utilisateur/relationships/avancements",
	// 		["tkres" => $tokenRessource],
	// 		[],
	// 		[],
	// 		["HTTP_Authorization" => "Bearer " . $this->token],
	// 	);

	// 	$this->assertResponseStatus(403);
	// }

	// public function test_étant_donné_un_token_expiré_et_un_token_ressource_valide_lorsquon_effectue_une_requête_on_obtient_401()
	// {
	// 	$ressources = json_encode([["url" => "\/user\/autre_utilisateur", "method" => "GET"]]);
	// 	$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);
	// 	$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", time() - 1, $ressources);

	// 	$this->call(
	// 		"GET",
	// 		"/user/autre_utilisateur",
	// 		["tkres" => $tokenRessource],
	// 		[],
	// 		[],
	// 		["HTTP_Authorization" => "Bearer " . $token],
	// 	);

	// 	$this->assertResponseStatus(401);
	// }

	// public function test_étant_donné_un_token_avec_une_mauvaise_signature_et_un_token_ressource_valide_lorsquon_effectue_une_requête_on_obtient_401()
	// {
	// 	$ressources = json_encode([["url" => "\/user\/autre_utilisateur", "method" => "GET"]]);
	// 	$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);
	// 	$payload = [
	// 		"username" => "utilisateur_lambda",
	// 		"current" => time(),
	// 		"expired" => 0,
	// 		"ressources" => $ressources,
	// 		"version" => 1,
	// 	];
	// 	$token = JWT::encode($payload, "mauvais_secret", "HS256");

	// 	$this->call(
	// 		"GET",
	// 		"/user/autre_utilisateur",
	// 		["tkres" => $tokenRessource],
	// 		[],
	// 		[],
	// 		["HTTP_Authorization" => "Bearer " . $token],
	// 	);

	// 	$this->assertResponseStatus(401);
	// }

	// public function test_étant_donné_un_token_valide_et_un_token_ressource_expiré_lorsquon_effectue_une_requête_on_obtient_403()
	// {
	// 	$ressources = json_encode([["url" => "\/user\/autre_utilisateur", "method" => "GET"]]);
	// 	$tokenRessource = GénérateurDeToken::get_instance()->générer_token(
	// 		"autre_utilisateur",
	// 		time() - 1,
	// 		$ressources,
	// 	);

	// 	$this->call(
	// 		"GET",
	// 		"/user/autre_utilisateur",
	// 		["tkres" => $tokenRessource],
	// 		[],
	// 		[],
	// 		["HTTP_Authorization" => "Bearer " . $this->token],
	// 	);

	// 	$this->assertResponseStatus(403);
	// }

	// public function test_étant_donné_un_token_valide_et_un_token_ressource_avec_une_mauvaise_signature_lorsquon_effectue_une_requête_on_obtient_403()
	// {
	// 	$ressources = json_encode([["url" => "\/user\/autre_utilisateur", "method" => "GET"]]);
	// 	$payload = [
	// 		"username" => "autre_utilisateur",
	// 		"current" => time(),
	// 		"expired" => 0,
	// 		"ressources" => $ressources,
	// 		"version" => 1,
	// 	];
	// 	$tokenRessource = JWT::encode($payload, "mauvais_secret", "HS256");

	// 	$this->call(
	// 		"GET",
	// 		"/user/autre_utilisateur",
	// 		["tkres" => $tokenRessource],
	// 		[],
	// 		[],
	// 		["HTTP_Authorization" => "Bearer " . $this->token],
	// 	);

	// 	$this->assertResponseStatus(403);
	// }

	// public function test_étant_donné_un_token_valide_et_un_token_ressource_mal_formaté_sans_ressources_lorsquon_effectue_une_requête_on_obtient_403()
	// {
	// 	$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, null);

	// 	$this->call(
	// 		"GET",
	// 		"/user/autre_utilisateur",
	// 		["tkres" => $tokenRessource],
	// 		[],
	// 		[],
	// 		["HTTP_Authorization" => "Bearer " . $this->token],
	// 	);

	// 	$this->assertResponseStatus(403);
	// }

	// public function test_étant_donné_un_token_valide_et_un_token_ressource_mal_formaté_sans_url_lorsquon_effectue_une_requête_on_obtient_403()
	// {
	// 	$ressources = json_encode([["url" => "", "method" => "*"]]);
	// 	$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

	// 	$this->call(
	// 		"GET",
	// 		"/user/autre_utilisateur",
	// 		["tkres" => $tokenRessource],
	// 		[],
	// 		[],
	// 		["HTTP_Authorization" => "Bearer " . $this->token],
	// 	);

	// 	$this->assertResponseStatus(403);
	// }

	// public function test_étant_donné_un_token_valide_et_un_token_ressource_mal_formaté_sans_méthode_lorsquon_effectue_une_requête_on_obtient_403()
	// {
	// 	$ressources = json_encode([["url" => "/^.*$/", "method" => ""]]);
	// 	$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

	// 	$this->call(
	// 		"GET",
	// 		"/user/autre_utilisateur",
	// 		["tkres" => $tokenRessource],
	// 		[],
	// 		[],
	// 		["HTTP_Authorization" => "Bearer " . $this->token],
	// 	);

	// 	$this->assertResponseStatus(403);
	// }
}