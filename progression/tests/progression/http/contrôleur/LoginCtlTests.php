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

require_once __DIR__ . "/../../../TestCase.php";

use progression\http\contrôleur\GénérateurDeToken;
use progression\domaine\entité\{User, Clé};
use progression\dao\DAOFactory;
use Illuminate\Http\Request;
use Illuminate\Auth\GenericUser;
use Firebase\JWT\JWT;

final class LoginCtlTests extends TestCase
{
	public $user;

	public function setUp(): void
	{
		parent::setUp();

		$this->user = new GenericUser(["username" => "bob", "rôle" => User::ROLE_NORMAL]);

		// UserDAO
		$mockUserDAO = Mockery::mock("progression\dao\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("bob")
			->andReturn(new User("bob"));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("Marcel")
			->andReturn(null);

		// CléDAO
		$mockCléDAO = Mockery::mock("progression\dao\CléDAO");
		$mockCléDAO
			->shouldReceive("get_clé")
			->with("bob", "clé valide")
			->andReturn(new Clé(null, (new \DateTime())->getTimestamp(), 0, Clé::PORTEE_AUTH));
		$mockCléDAO
			->shouldReceive("vérifier")
			->with("bob", "clé valide", "secret")
			->andReturn(true);
		$mockCléDAO
			->shouldReceive("get_clé")
			->with("bob", "clé invalide")
			->andReturn(null);
		$mockCléDAO->shouldReceive("vérifier")->andReturn(false);

		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		$mockDAOFactory->shouldReceive("get_clé_dao")->andReturn($mockCléDAO);
		DAOFactory::setInstance($mockDAOFactory);

		//Mock du générateur de token
		GénérateurDeToken::set_instance(
			new class extends GénérateurDeToken {
				public function __construct()
				{
				}

				function générer_token($user)
				{
					return "token valide";
				}
			},
		);
	}

	public function tearDown(): void
	{
		Mockery::close();
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
				return $user->username == "Marcel" && $user->rôle == User::ROLE_NORMAL;
			})
			->andReturn(new User("Marcel"));

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
			}, "password")
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
			"key_name" => "clé valide",
			"key_secret" => "secret",
		]);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertEquals('{"Token":"token valide"}', $résultat_observé->getContent());
	}

	public function test_étant_donné_lutilisateur_Bob_et_une_clé_dauthentification_invalide_lorsquon_login_on_obtient_une_erreur_401()
	{
		$résultat_observé = $this->call("POST", "/auth", [
			"username" => "bob",
			"key_name" => "clé invalide",
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
			'{"erreur":{"password":["The password field is required when key name is not present."]}}',
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
			'{"erreur":{"password":["The password field is required when key name is not present."]}}',
			$résultat_observé->content(),
		);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_login_avec_un_secret_vide_on_obtient_une_erreur_400()
	{
		$résultat_observé = $this->call("POST", "/auth", [
			"username" => "bob",
			"key_name" => "clé valide",
			"key_secret" => "",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"key_secret":["The key secret field is required when key name is present."]}}',
			$résultat_observé->content(),
		);
	}
	public function test_étant_donné_un_utilisateur_existant_lorsquon_login_sans_secret_on_obtient_une_erreur_400()
	{
		$résultat_observé = $this->call("POST", "/auth", [
			"username" => "bob",
			"key_name" => "clé valide",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"key_secret":["The key secret field is required when key name is present."]}}',
			$résultat_observé->content(),
		);
	}

	//Intestable tant que la connexion à LDAP se fera à même l'interacteur
	/*
	   public function test_étant_donné_lutilisateur_inexistant_roger_et_une_authentification_de_type_no_lorsquon_appelle_login_on_obtient_un_code_403()
	   {
	   $_ENV['AUTH_TYPE'] = "ldap";
	   $_ENV['JWT_SECRET'] = "secret";
	   $_ENV['JWT_TTL'] = 3333;

	   $résultat_observé = $this->actingAs($this->user)->call(
	   "POST",
	   "/auth",
	   ["username"=>"marcel", "password"=>"test"]
	   );
	   
	   $this->assertEquals(403, $résultat_observé->status());
	   $this->assertEquals('{"erreur":"Accès refusé."}', $résultat_observé->getContent());
	   }
	 */
}
