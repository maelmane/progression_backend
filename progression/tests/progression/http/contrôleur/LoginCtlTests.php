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
use progression\domaine\entité\{User, Clé};
use progression\dao\DAOFactory;
use Illuminate\Auth\GenericUser;

final class LoginCtlTests extends ContrôleurTestCase
{
	public $user;

	public function setUp(): void
	{
		parent::setUp();

		$this->user = new GenericUser(["username" => "bob", "rôle" => User::RÔLE::NORMAL]);

		// UserDAO
		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("bob")
			->andReturn(new User("bob", ÉTAT::ACTIF));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("bob", [])
			->andReturn(new User("bob"));
		$mockUserDAO->shouldReceive("get_user")->andReturn(null);

		// CléDAO
		$mockCléDAO = Mockery::mock("progression\\dao\\CléDAO");
		$mockCléDAO
			->shouldReceive("get_clé")
			->with("bob", "cleValide01")
			->andReturn(new Clé(null, (new \DateTime())->getTimestamp(), 0, Clé::PORTEE_AUTH));
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

		//Mock du générateur de token
		GénérateurDeToken::set_instance(
			new class extends GénérateurDeToken {
				public function __construct()
				{
				}

				function générer_token($user, $ressources = null, $expiration = 0)
				{
					return "token valide";
				}
			},
		);
	}

	public function tearDown(): void
	{
		Mockery::close();
		GénérateurDeToken::set_instance(null);
	}

	#  AUTH LDAP
	public function test_étant_donné_un_utilisateur_inexistant_avec_authentification_LDAP_lorsquon_appelle_login_lutilisateur_sans_domaine_on_obtient_une_erreur_401()
	{
		putenv("AUTH_LOCAL=false");
		putenv("AUTH_LDAP=true");

		$résultat_observé = $this->call("POST", "/auth", ["username" => "Marcel", "password" => "password"]);

		$this->assertEquals(401, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Accès interdit."}', $résultat_observé->getContent());
	}

	#  Aucune AUTH
	public function test_étant_donné_lutilisateur_Bob_sans_authentification_lorsquon_appelle_login_on_obtient_un_token_pour_lutilisateur_Bob()
	{
		putenv("AUTH_LOCAL=false");
		putenv("AUTH_LDAP=false");

		$mockUserDAO = DAOFactory::getInstance()->get_user_dao();
		$mockUserDAO
			->shouldReceive("vérifier_password")
			->withArgs(function ($user) {
				return $user->username == "bob";
			})
			->andReturn(true);

		$résultat_observé = $this->call("POST", "/auth", ["username" => "bob"]);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertEquals('{"Token":"token valide"}', $token = $résultat_observé->getContent());
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

		$résultat_observé = $this->call("POST", "/auth", ["username" => "Marcel"]);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertEquals('{"Token":"token valide"}', $résultat_observé->getContent());
	}

	# AUTH locale
	public function test_étant_donné_lutilisateur_Bob_avec_authentification_lorsquon_appelle_login_avec_mdp_correct_on_obtient_un_token_pour_lutilisateur_Bob()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$mockUserDAO = DAOFactory::getInstance()->get_user_dao();
		$mockUserDAO
			->shouldReceive("vérifier_password")
			->withArgs(function ($user) {
				return $user->username == "bob";
			})
			->andReturn(true);

		$résultat_observé = $this->call("POST", "/auth", ["username" => "bob", "password" => "test"]);
		$this->assertEquals(200, $résultat_observé->status());
		$this->assertEquals('{"Token":"token valide"}', $résultat_observé->getContent());
	}

	public function test_étant_donné_un_utilisateur_inexistant_avec_authentification_lorsquon_appelle_login_lutilisateur_on_obtient_une_erreur_401()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->call("POST", "/auth", ["username" => "Marcel", "password" => "test"]);

		$this->assertEquals(401, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Accès interdit."}', $résultat_observé->getContent());
	}

	public function test_étant_donné_un_utilisateur_Bob_avec_authentification_lorsquon_appelle_login_lutilisateur_avec_mdp_erroné_on_obtient_une_erreur_401()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$mockUserDAO = DAOFactory::getInstance()->get_user_dao();
		$mockUserDAO
			->shouldReceive("vérifier_password")
			->withArgs(function ($user) {
				return $user->username == "bob";
			}, "incorrect")
			->andReturn(false);

		$résultat_observé = $this->call("POST", "/auth", ["username" => "bob", "password" => "incorrect"]);

		$this->assertEquals(401, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Accès interdit."}', $résultat_observé->getContent());
	}

	# Identifiants invalides

	public function test_étant_donné_une_authentificaton_locale_lorsquon_appelle_login_avec_un_nom_dutilisateur_vide_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->call("POST", "/auth", ["username" => "", "password" => "test"]);

		$this->assertEquals(400, $résultat_observé->status());
	}

	public function test_étant_donné_une_authentificaton_locale_lorsquon_appelle_login_avec_un_nom_dutilisateur_invalide_on_obtient_une_erreur_401()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->call("POST", "/auth", ["username" => "bo bo", "password" => "test"]);

		$this->assertEquals(400, $résultat_observé->status());
	}

	public function test_étant_donné_une_authentificaton_locale_lorsquon_appelle_login_sans_nom_dutlisateur_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->call("POST", "/auth", ["password" => "test"]);

		$this->assertEquals(400, $résultat_observé->status());
	}

	public function test_étant_donné_une_authentification_locale_lorsquon_appelle_login_sans_mot_de_passe_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->call("POST", "/auth", ["username" => ""]);

		$this->assertEquals(400, $résultat_observé->status());
	}

	public function test_étant_donné_une_authentification_locale_lorsquon_appelle_login_avec_mot_de_passe_vide_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->call("POST", "/auth", ["username" => "bob", "password" => ""]);

		$this->assertEquals(400, $résultat_observé->status());
	}

	# Authentification par clé
	public function test_étant_donné_lutilisateur_Bob_et_une_clé_dauthentification_valide_lorsquon_login_on_obtient_un_token_pour_lutilisateur_Bob()
	{
		$résultat_observé = $this->call("POST", "/auth", [
			"username" => "bob",
			"key_name" => "cleValide01",
			"key_secret" => "secret",
		]);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertEquals('{"Token":"token valide"}', $résultat_observé->getContent());
	}

	public function test_étant_donné_lutilisateur_Bob_et_une_clé_dauthentification_invalide_lorsquon_login_on_obtient_une_erreur_401()
	{
		$résultat_observé = $this->call("POST", "/auth", [
			"username" => "bob",
			"key_name" => "cleInvalide00",
			"key_secret" => "secret",
		]);

		$this->assertEquals(401, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Accès interdit."}', $résultat_observé->content());
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_login_avec_une_clé_vide_on_obtient_une_erreur_400()
	{
		$résultat_observé = $this->call("POST", "/auth", [
			"username" => "bob",
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
			"username" => "bob",
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
			"username" => "bob",
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
			"username" => "bob",
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
			"username" => "bob",
			"key_name" => "cleValide01",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"key_secret":["Err: 1004. Le champ key_secret est obligatoire lorsque key_name est présent."]}}',
			$résultat_observé->content(),
		);
	}
}
