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

use progression\ContrôleurTestCase;

use progression\http\contrôleur\GénérateurDeToken;
use progression\domaine\entité\clé\{Clé, Portée};
use progression\domaine\entité\user\{User, Rôle, État};
use progression\dao\DAOFactory;
use Illuminate\Auth\GenericUser;
use Carbon\Carbon;

final class LoginCtlTests extends ContrôleurTestCase
{
	public $user;

	public function setUp(): void
	{
		parent::setUp();

		$this->user = new GenericUser([
			"username" => "bob",
			"rôle" => Rôle::NORMAL,
			"état" => État::ACTIF,
		]);

		putenv("APP_URL=https://example.com/");
		putenv("JWT_SECRET=secret");
		putenv("JWT_TTL=86400");
		putenv("APP_VERSION=1.2.3");

		Carbon::setTestNowAndTimezone(Carbon::create(2001, 5, 21, 12));

		// UserDAO
		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("bob")
			->andReturn(new User("bob", état: État::ACTIF));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("bob", [])
			->andReturn(new User("bob", état: État::ACTIF));
		$mockUserDAO
			->shouldReceive("trouver")
			->with(null, "bob@progressionmail.com", [])
			->andReturn(new User("bob", état: État::ACTIF));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("roger")
			->andReturn(new User("roger", état: État::INACTIF));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("marcel")
			->andReturn(new User("marcel", état: État::ATTENTE_DE_VALIDATION));
		$mockUserDAO->shouldReceive("get_user")->andReturn(null);

		$mockUserDAO
			->shouldReceive("vérifier_password")
			->with(Mockery::Any(), "incorrect")
			->andReturn(false);
		$mockUserDAO
			->shouldReceive("vérifier_password")
			->with(Mockery::Any(), "m0tD3P4ZZE")
			->andReturn(true);

		// CléDAO
		$mockCléDAO = Mockery::mock("progression\\dao\\CléDAO");
		$mockCléDAO
			->shouldReceive("get_clé")
			->with("bob", "cleValide01")
			->andReturn(new Clé(null, (new \DateTime())->getTimestamp(), 0, Portée::AUTH));
		$mockCléDAO
			->shouldReceive("vérifier")
			->with("bob", "cleValide01", "secret")
			->andReturn(true);
		$mockCléDAO->shouldReceive("get_clé")->andReturn(null);
		$mockCléDAO->shouldReceive("vérifier")->andReturn(false);

		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		$mockDAOFactory->shouldReceive("get_clé_dao")->andReturn($mockCléDAO);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
		GénérateurDeToken::set_instance(null);
	}

	#  AUTH LDAP
	# Ces tests nécessitent que LDAP soit découplé de l'interacteur: https://git.dti.crosemont.quebec/progression/progression_backend/-/issues/79
	//public function test_étant_donné_un_utilisateur_existant_et_une_authentification_LDAP_lorsquon_login_avec_identifiant_et_mdp_corrects_on_obtient_un_token_valide()
	//{
	//	putenv("AUTH_LOCAL=false");
	//	putenv("AUTH_LDAP=true");
	//
	//	$résultat_observé = $this->call("POST", "/auth", [
	//		"identifiant" => "bob",
	//		"password" => "m0tD3P4ZZE",
	//	]);
	//
	//    $this->assertEquals(200, $résultat_observé->status());
	//	$this->assertEquals('{"Token":"token valide"}', $token = $résultat_observé->getContent());
	//}
	//
	//public function test_étant_donné_un_utilisateur_existant_et_une_authentification_LDAP_lorsquon_login_avec_courriel_et_mdp_corrects_on_obtient_un_token_valide()
	//{
	//	putenv("AUTH_LOCAL=false");
	//	putenv("AUTH_LDAP=true");
	//
	//	$résultat_observé = $this->call("POST", "/auth", [
	//		"identifiant" => "bob@progressionmail.com",
	//		"password" => "m0tD3P4ZZE",
	//	]);
	//
	//	$this->assertEquals(200, $résultat_observé->status());
	//	$this->assertEquals('{"Token":"token valide"}', $token = $résultat_observé->getContent());
	//}

	public function test_étant_donné_un_utilisateur_inexistant_avec_authentification_LDAP_lorsquon_appelle_login_lutilisateur_sans_domaine_on_obtient_une_erreur_401()
	{
		putenv("AUTH_LOCAL=false");
		putenv("AUTH_LDAP=true");

		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "Marcel",
			"password" => "m0tD3P4ZZE",
		]);

		$this->assertEquals(401, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Accès interdit."}', $résultat_observé->getContent());
	}

	public function test_étant_donné_une_authentification_LDAP_lorsquon_appelle_login_lutilisateur_avec_un_nom_dutilisateur_invalide_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=false");
		putenv("AUTH_LDAP=true");

		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "Marcel@test@ici.com",
			"password" => "password",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"identifiant":["L\'identifiant doit être un nom d\'utilisateur ou un courriel valide."]}}',
			$résultat_observé->getContent(),
		);
	}

	#  Aucune AUTH
	public function test_étant_donné_un_utilisateur_existant_et_sans_authentification_lorsquon_login_avec_identifiant_on_obtient_un_token_valide()
	{
		putenv("AUTH_LOCAL=false");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->call("POST", "/auth", ["identifiant" => "bob"]);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/token_authentification.json",
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_utilisateur_inexistant_sans_authentification_lorsquon_appelle_login_lutilisateur_avec_un_courriel_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=false");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "marcel@ici.com",
			"password" => "password",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"identifiant":["L\'identifiant doit être un nom d\'utilisateur ou un courriel valide."]}}',
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_utilisateur_inexistant_sans_authentification_lorsquon_appelle_login_lutilisateur_est_créé()
	{
		putenv("AUTH_LOCAL=false");
		putenv("AUTH_LDAP=false");

		$mockUserDAO = DAOFactory::getInstance()->get_user_dao();
		$mockUserDAO
			->shouldReceive("save")
			->once()
			->withArgs(function ($user) {
				return $user->username == "Marcel";
			})
			->andReturnArg(0);

		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "Marcel",
		]);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertNotNull($résultat_observé->getContent());
	}

	# AUTH locale
	public function test_étant_donné_un_utilisateur_existant_et_une_authentification_locale_lorsquon_login_avec_identifiant_et_mdp_corrects_on_obtient_un_token_valide()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "bob",
			"password" => "m0tD3P4ZZE",
		]);
		$this->assertEquals(200, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/token_authentification.json",
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_utilisateur_existant_et_une_authentificaton_locale_lorsquon_login_avec_courriel_et_mdp_corrects_on_obtient_un_token_valide()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "bob@progressionmail.com",
			"password" => "m0tD3P4ZZE",
		]);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/token_authentification.json",
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_utilisateur_inactif_avec_authentification_lorsquon_appelle_login_avec_mdp_correct_on_obtient_une_erreur_401()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "roger",
			"password" => "m0tD3P4ZZE",
		]);
		$this->assertEquals(401, $résultat_observé->status());
	}

	public function test_étant_donné_un_utilisateur_en_attente_de_validation_avec_authentification_lorsquon_appelle_login_avec_mdp_correct_on_obtient_une_erreur_401()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "marcel",
			"password" => "m0tD3P4ZZE",
		]);
		$this->assertEquals(401, $résultat_observé->status());
	}

	public function test_étant_donné_un_utilisateur_inexistant_avec_authentification_lorsquon_login_avec_identifiant_on_obtient_une_erreur_401()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "Zozo",
			"password" => "m0tD3P4ZZE",
		]);

		$this->assertEquals(401, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Accès interdit."}', $résultat_observé->getContent());
	}

	public function test_étant_donné_un_utilisateur_existant_avec_authentification_lorsquon_login_avec_identifiant_et_mdp_erroné_on_obtient_une_erreur_401()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "bob",
			"password" => "incorrect",
		]);

		$this->assertEquals(401, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Accès interdit."}', $résultat_observé->getContent());
	}

	# Identifiants invalides

	public function test_étant_donné_une_authentificaton_locale_lorsquon_appelle_login_avec_un_nom_dutilisateur_vide_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "",
			"password" => "m0tD3P4ZZE",
		]);

		$this->assertEquals(400, $résultat_observé->status());
	}

	public function test_étant_donné_une_authentificaton_locale_lorsquon_appelle_login_avec_un_nom_dutilisateur_invalide_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "bo bo",
			"password" => "m0tD3P4ZZE",
		]);

		$this->assertEquals(400, $résultat_observé->status());
	}

	public function test_étant_donné_une_authentificaton_locale_lorsquon_appelle_login_sans_nom_dutlisateur_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->call("POST", "/auth", [
			"password" => "m0tD3P4ZZE",
		]);

		$this->assertEquals(400, $résultat_observé->status());
	}

	public function test_étant_donné_une_authentification_locale_lorsquon_appelle_login_sans_mot_de_passe_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->call("POST", "/auth", ["identifiant" => ""]);

		$this->assertEquals(400, $résultat_observé->status());
	}

	public function test_étant_donné_une_authentification_locale_lorsquon_appelle_login_avec_mot_de_passe_vide_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "bob",
			"password" => "",
		]);

		$this->assertEquals(400, $résultat_observé->status());
	}

	# Authentification par clé
	public function test_étant_donné_un_utilisateur_existant_et_une_clé_dauthentification_valide_lorsquon_login_on_obtient_un_token_pour_lutilisateur()
	{
		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "bob",
			"key_name" => "cleValide01",
			"key_secret" => "secret",
		]);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/token_authentification.json",
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_utilisateur_existant_et_une_clé_dauthentification_valide_lorsquon_login_avec_un_identifiant_invalide_on_obtient_une_erreur_400()
	{
		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "bob@progressionmail.com",
			"key_name" => "cleValide01",
			"key_secret" => "secret",
		]);

		$this->assertEquals(400, $résultat_observé->status());
	}

	public function test_étant_donné_un_utilisateur_existant_et_une_clé_dauthentification_invalide_lorsquon_login_on_obtient_une_erreur_401()
	{
		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "bob",
			"key_name" => "cleInvalide00",
			"key_secret" => "secret",
		]);

		$this->assertEquals(401, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Accès interdit."}', $résultat_observé->content());
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_login_avec_une_clé_vide_on_obtient_une_erreur_400()
	{
		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "bob",
			"key_name" => "",
			"key_secret" => "secret",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"password":["Err: 1004. Le champ password est obligatoire lorsque key_name n\'est pas présent."]}}',
			$résultat_observé->content(),
		);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_login_avec_une_clé_au_nom_invalide_on_obtient_une_erreur_400()
	{
		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "bob",
			"key_name" => "tata toto",
			"key_secret" => "secret",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"key_name":["Err: 1003. Le champ key_name doit être alphanumérique \'a-Z0-9-_\'"]}}',
			$résultat_observé->content(),
		);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_login_sans_clé_on_obtient_une_erreur_400()
	{
		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "bob",
			"key_secret" => "secret",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"password":["Err: 1004. Le champ password est obligatoire lorsque key_name n\'est pas présent."]}}',
			$résultat_observé->content(),
		);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_login_avec_un_secret_vide_on_obtient_une_erreur_400()
	{
		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "bob",
			"key_name" => "cleValide01",
			"key_secret" => "",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"key_secret":["Err: 1004. Le champ key_secret est obligatoire lorsque key_name est présent."]}}',
			$résultat_observé->content(),
		);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_login_sans_secret_on_obtient_une_erreur_400()
	{
		$résultat_observé = $this->call("POST", "/auth", [
			"identifiant" => "bob",
			"key_name" => "cleValide01",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"key_secret":["Err: 1004. Le champ key_secret est obligatoire lorsque key_name est présent."]}}',
			$résultat_observé->content(),
		);
	}
}
