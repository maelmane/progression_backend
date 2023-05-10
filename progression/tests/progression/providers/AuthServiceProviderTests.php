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
use progression\domaine\entité\user\{User, État, Rôle};
use Illuminate\Auth\GenericUser;
use progression\http\contrôleur\NotImplementedCtl;
use Firebase\JWT\JWT;

final class AuthServiceProviderTests extends TestCase
{
	public $utilisateurActifNormal;
	public $utilisateurInactifNormal;
	public $utilisateurEnAttenteNormal;
	public $tokenUtilisateurActifNormal;
	public $tokenUtilisateurInactifNormal;
	public $tokenUtilisateurEnAttenteNormal;

	public function setUp(): void
	{
		parent::setUp();

		$this->utilisateurActifNormal = new GenericUser([
			"username" => "utilisateur_actif_normal",
			"rôle" => Rôle::NORMAL,
			"état" => État::ACTIF,
		]);
		$this->utilisateurInactifNormal = new GenericUser([
			"username" => "utilisateur_inactif_normal",
			"rôle" => Rôle::NORMAL,
			"état" => État::INACTIF,
		]);
		$this->utilisateurEnAttenteNormal = new GenericUser([
			"username" => "utilisateur_en_attente_normal",
			"rôle" => Rôle::NORMAL,
			"état" => État::ATTENTE_DE_VALIDATION,
		]);
		$this->tokenUtilisateurActifNormal = GénérateurDeToken::get_instance()->générer_token(
			"utilisateur_actif_normal",
		);
		$this->tokenUtilisateurInactifNormal = GénérateurDeToken::get_instance()->générer_token(
			"utilisateur_inactif_normal",
		);
		$this->tokenUtilisateurEnAttenteNormal = GénérateurDeToken::get_instance()->générer_token(
			"utilisateur_en_attente_normal",
		);

		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("utilisateur_actif_normal", [])
			->andReturn(new User(username: "utilisateur_actif_normal", état: État::ACTIF, rôle: Rôle::NORMAL));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("utilisateur_inactif_normal", [])
			->andReturn(new User(username: "utilisateur_inactif_normal", état: État::INACTIF, rôle: Rôle::NORMAL));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("utilisateur_en_attente_normal", [])
			->andReturn(
				new User(
					username: "utilisateur_en_attente_normal",
					état: État::ATTENTE_DE_VALIDATION,
					rôle: Rôle::NORMAL,
				),
			);
		$mockUserDAO
			->shouldReceive("get_user")
			->with("UTILISATEUR_ACTIF_NORMAL", [])
			->andReturn(new User(username: "utilisateur_actif_normal", état: État::ACTIF, rôle: Rôle::NORMAL));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("autre_utilisateur", [])
			->andReturn(new User(username: "autre_utilisateur", état: État::ACTIF, rôle: Rôle::NORMAL));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("utilisateur_innocent", [])
			->andReturn(new User(username: "utilisateur_innocent", état: État::ACTIF, rôle: Rôle::NORMAL));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("utilisateur_malveillant", [])
			->andReturn(new User(username: "utilisateur_malveillant", état: État::ACTIF, rôle: Rôle::NORMAL));
		$mockUserDAO->shouldReceive("get_user")->andReturn(null);
		$mockUserDAO->shouldReceive("vérifier_password")->andReturn(true);
		$mockUserDAO->shouldReceive("save")->andReturnArg(0);

		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_lorsquon_utilise_un_token_avec_un_nom_dutilisateur_avec_une_casse_différente_on_obtient_un_code_200()
	{
		$expiration = time() + 1;
		$tokenUtilisateurActifNormal = GénérateurDeToken::get_instance()->générer_token(
			"utilisateur_actif_normal",
			$expiration,
		);
		$this->call(
			"GET",
			"/user/UTILISATEUR_ACTIF_NORMAL",
			[],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurActifNormal],
		);

		$this->assertResponseStatus(200);
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_lorsque_le_token_expire_dans_1_seconde_on_obtient_un_code_200()
	{
		$expiration = time() + 1;
		$tokenUtilisateurActifNormal = GénérateurDeToken::get_instance()->générer_token(
			"utilisateur_actif_normal",
			$expiration,
		);
		$this->call(
			"GET",
			"/user/utilisateur_actif_normal",
			[],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurActifNormal],
		);

		$this->assertResponseStatus(200);
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_lorsque_la_date_dexpiration_du_token_est_0_on_obtient_un_code_200()
	{
		$expiration = 0;
		$tokenUtilisateurActifNormal = GénérateurDeToken::get_instance()->générer_token(
			"utilisateur_actif_normal",
			$expiration,
		);
		$this->call(
			"GET",
			"/user/utilisateur_actif_normal",
			[],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurActifNormal],
		);

		$this->assertResponseStatus(200);
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_existant_lorsque_la_date_dexpiration_est_échue_depuis_1_seconde_on_obtient_une_erreur_401()
	{
		$expiration = time() - 1;
		$tokenUtilisateurActifNormal = GénérateurDeToken::get_instance()->générer_token(
			"utilisateur_actif_normal",
			$expiration,
		);
		$this->call(
			"GET",
			"/user/utilisateur_actif_normal",
			[],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurActifNormal],
		);

		$this->assertResponseStatus(401);
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_inexistant_lorsque_lorsquon_tente_dacceder_à_une_ressource_on_obtient_une_erreur_401()
	{
		$tokenUtilisateurActifNormal = GénérateurDeToken::get_instance()->générer_token("utilisateur_inexistant");
		$this->call(
			"GET",
			"/user/utilisateur_actif_normal",
			[],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurActifNormal],
		);

		$this->assertResponseStatus(401);
	}

	public function test_étant_donné_un_token_avec_un_url_lorsquon_effectue_une_requête_a_un_url_valide_on_obtient_un_code_200()
	{
		$ressources = [["url" => "^user/utilisateur_actif_normal$", "method" => "get"]];
		$tokenUtilisateurActifNormal = GénérateurDeToken::get_instance()->générer_token(
			"utilisateur_actif_normal",
			0,
			$ressources,
		);
		$résultatObtenu = $this->call(
			"GET",
			"/user/utilisateur_actif_normal",
			[],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurActifNormal],
		);

		$this->assertResponseStatus(200);
	}

	public function test_étant_donné_un_token_avec_un_url_lorsquon_effectue_une_requête_a_un_url_valide_mais_non_autorisée_on_obtient_un_code_403()
	{
		$ressources = [["url" => "^user/utilisateur_actif_normal$", "method" => "post"]];
		$tokenUtilisateurActifNormal = GénérateurDeToken::get_instance()->générer_token(
			"utilisateur_actif_normal",
			0,
			$ressources,
		);
		$résultatObtenu = $this->call(
			"POST",
			"/user/utilisateur_actif_normal/cles",
			[],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurActifNormal],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_token_pour_un_utilisateur_actif_normal_lorsquon_effectue_une_requête_pour_accéder_aux_ressources_dun_autre_utilisateur_on_obtient_une_erreur_403()
	{
		$résultatObtenu = $this->call(
			"GET",
			"/user/autre_utilisateur",
			[],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurActifNormal],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_token_ressource_qui_contient_différents_url_lorsquon_effectue_une_requête_à_une_ressource_autorisée_on_obtient_200()
	{
		$ressources = [
			["url" => "^autre/ressource_test$", "method" => "get"],
			["url" => "^user/autre_utilisateur$", "method" => "get"],
			["url" => "^ressource/autre_test$", "method" => ".*"],
		];
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

		$this->call(
			"GET",
			"user/autre_utilisateur",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurActifNormal],
		);

		$this->assertResponseStatus(200);
	}

	public function test_étant_donné_un_token_ressource_qui_contient_différents_url_lorsquon_effectue_une_requête_à_une_ressource_non_autorisée_on_obtient_403()
	{
		$ressources = [
			[
				"url" => "^user/autre_utilisateur/avancements$",
				"method" => "get",
			],
			["url" => "^user/autre_utilisateur$", "method" => "get"],
			[
				"url" => "^user/autre_utilisateur/relationships/avancement$/",
				"method" => "post",
			],
		];
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

		$this->call(
			"POST",
			"/user/autre_utilisateur/cles",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurActifNormal],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_utilisateur_malveillant_lorsquon_fabrique_un_token_pour_accéder_aux_ressources_dun_utilisateur_innocent_on_obtient_une_erreur_403()
	{
		$tokenUtilisateurMalveillant = GénérateurDeToken::get_instance()->générer_token("utilisateur_malveillant");

		$ressourcesUtilisateurInnocent = [["url" => "^user\/utilisateur_innocent$", "method" => "post"]];

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
			["tkres" => $tokenUtilisateurMalveillant],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurMalveillant],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_token_ressource_qui_contient_seulement_une_méthode_POST_lorsquon_effectue_une_requête_avec_GET_on_obtient_un_code_403()
	{
		$ressources = [["url" => ".*", "", "method" => "post"]];
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

		$this->call(
			"GET",
			"/user/autre_utilisateur",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurActifNormal],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_token_expiré_et_un_token_ressource_valide_lorsquon_effectue_une_requête_on_obtient_401()
	{
		$tokenUtilisateurActifNormal = GénérateurDeToken::get_instance()->générer_token(
			"utilisateur_actif_normal",
			time() - 1,
		);
		$ressources = [["url" => "^user/autre_utilisateur$", "method" => "get"]];
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

		$this->call(
			"GET",
			"/user/autre_utilisateur",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurActifNormal],
		);

		$this->assertResponseStatus(401);
	}

	public function test_étant_donné_un_token_valide_et_un_token_ressource_expiré_lorsquon_effectue_une_requête_pour_ses_propres_ressources_on_obtient_200()
	{
		$tokenUtilisateurActifNormal = GénérateurDeToken::get_instance()->générer_token("utilisateur_actif_normal");
		$ressources = [["url" => "^user/autre_utilisateur$", "method" => "get"]];
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token(
			"autre_utilisateur",
			time() - 1,
			$ressources,
		);

		$this->call(
			"GET",
			"/user/utilisateur_actif_normal",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurActifNormal],
		);

		$this->assertResponseStatus(200);
	}

	public function test_étant_donné_un_token_avec_une_mauvaise_signature_et_un_token_ressource_valide_lorsquon_effectue_une_requête_on_obtient_401()
	{
		$ressources = [["url" => "^user/autre_utilisateur$", "method" => "get"]];
		$payload = [
			"username" => "utilisateur_actif_normal",
			"current" => time(),
			"expired" => 0,
			"ressources" => $ressources,
			"version" => 1,
		];

		$tokenUtilisateurActifNormal = JWT::encode($payload, "mauvais_secret", "HS256");
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

		$this->call(
			"GET",
			"/user/autre_utilisateur",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $tokenUtilisateurActifNormal],
		);

		$this->assertResponseStatus(401);
	}

	public function test_étant_donné_un_token_valide_et_un_token_ressource_expiré_lorsquon_requiert_une_ressource_dun_autre_utilisateur_on_obtient_403()
	{
		$ressources = [["url" => "^user/autre_utilisateur$", "method" => "get"]];
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
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurActifNormal],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_token_valide_et_un_token_ressource_avec_une_mauvaise_signature_lorsquon_requiert_une_ressource_dun_autre_utilisateur_on_obtient_403()
	{
		$ressources = [["url" => "^user/autre_utilisateur$", "method" => "get"]];
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
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurActifNormal],
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
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurActifNormal],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_token_valide_et_un_token_ressource_mal_formaté_sans_url_lorsquon_requiert_une_ressource_dun_autre_utilisateur_on_obtient_403()
	{
		$ressources = [["url" => "", "method" => ".*"]];
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

		$this->call(
			"GET",
			"/user/autre_utilisateur",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurActifNormal],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_token_valide_et_un_token_ressource_mal_formaté_sans_méthode_lorsquon_requiert_une_ressource_dun_autre_utilisateur_on_obtient_403()
	{
		$ressources = [["url" => ".*", "method" => ""]];
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token("autre_utilisateur", 0, $ressources);

		$this->call(
			"GET",
			"/user/autre_utilisateur",
			["tkres" => $tokenRessource],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurActifNormal],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_utilisateur_inactif_lorsquon_tente_de_lauthentifier__on_obtient_une_erreur_401()
	{
		$ressources = [["url" => ".*", "method" => ""]];
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token(
			"utilisateur_inactif_normal",
			0,
			$ressources,
		);

		$this->call(
			"POST",
			"/auth",
			[
				"username" => "utilisateur_inactif_normal",
				"password" => "password",
			],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurInactifNormal],
		);

		$this->assertResponseStatus(401);
	}

	public function test_étant_donné_un_utilisateur_inactif_et_un_token_valide_lorsquon_requiert_une_ressource_protégée_on_obtient_une_erreur_403()
	{
		$ressources = [["url" => ".*", "method" => ""]];
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token(
			"utilisateur_inactif_normal",
			0,
			$ressources,
		);

		$this->call(
			"POST",
			"/user/utilisateur_inactif_normal",
			[],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurInactifNormal],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_utilisateur_inactif_et_un_token_valide_lorsquon_requiert_une_ressource_non_protégée_on_obtient_un_code_200()
	{
		$ressources = [["url" => ".*", "method" => ""]];
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token(
			"utilisateur_inactif_normal",
			0,
			$ressources,
		);

		$this->call(
			"GET",
			"/user/utilisateur_inactif_normal",
			[],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurInactifNormal],
		);

		$this->assertResponseStatus(403);
	}

	public function test_étant_donné_un_utilisateur_non_validé_lorsquon_tente_de_lauthentifier_on_obtient_un_code_401()
	{
		$this->call(
			"POST",
			"/auth",
			[
				"username" => "utilisateur_en_attente_normal",
				"password" => "password",
			],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurEnAttenteNormal],
		);

		$this->assertResponseStatus(401);
	}

	public function test_étant_donné_un_utilisateur_non_validé_et_un_token_ressource_valide_lorsquon_tente_de_modifier_son_état_on_obtient_un_code_200()
	{
		$ressources = [["url" => "/user/utilisateur_en_attente_normal", "method" => "^POST$"]];
		$tokenRessource = GénérateurDeToken::get_instance()->générer_token(
			"utilisateur_inactif_normal",
			0,
			$ressources,
		);

		$this->call(
			"POST",
			"/user/utilisateur_en_attente_normal",
			[
				"état" => "1",
			],
			[],
			[],
			["HTTP_Authorization" => "Bearer " . $this->tokenUtilisateurEnAttenteNormal],
		);

		$this->assertResponseStatus(200);
	}
}
