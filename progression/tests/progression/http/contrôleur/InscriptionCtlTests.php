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

use progression\http\contrôleur\GénérateurDeToken;
use progression\domaine\entité\User;
use progression\dao\DAOFactory;
use Illuminate\Auth\GenericUser;

final class InscriptionCtlTests extends TestCase
{
	public $user;

	public function setUp(): void
	{
		parent::setUp();

		putenv("AUTH_LDAP=false");

		$this->user = new GenericUser(["username" => "bob", "rôle" => User::ROLE_NORMAL]);

		// UserDAO
		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("bob")
			->andReturn(new User("bob"));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("Marcel")
			->andReturn(null);

		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		DAOFactory::setInstance($mockDAOFactory);

		//$mockGénérateurDeToken = Mockery::mock("")

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

	#  AUTH_LOCAL = false
	public function test_étant_donné_un_utilisateur_existant_sans_authentification_lorsquon_inscrit_de_nouveau_on_obtient_un_token_pour_lutilisateur()
	{
		putenv("AUTH_LOCAL=false");

		$résultat_observé = $this->call("POST", "/inscription", ["username" => "bob"]);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertEquals('{"Token":"token valide"}', $résultat_observé->getContent());
	}

	public function test_étant_donné_un_utilisateur_inexistant_sans_authentification_lorsquon_linscrit_il_est_sauvegardé_et_on_obtient_un_token()
	{
		putenv("AUTH_LOCAL=false");

		$mockUserDAO = DAOFactory::getInstance()->get_user_dao();
		$mockUserDAO
			->shouldReceive("save")
			->once()
			->withArgs(function ($user) {
				return $user->username == "Marcel" && $user->rôle == User::ROLE_NORMAL;
			})
			->andReturn(new User("Marcel"));

		$résultat_observé = $this->call("POST", "/inscription", ["username" => "Marcel"]);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertEquals('{"Token":"token valide"}', $résultat_observé->getContent());
	}

	# AUTH_LOCAL = true
	public function test_étant_donné_un_utilisateur_existant_avec_authentification_lorsquon_linscrit_de_nouveau_on_obtient_une_erreur_403()
	{
		putenv("AUTH_LOCAL=true");

		$résultat_observé = $this->call("POST", "/inscription", ["username" => "bob", "password" => "test"]);

		$this->assertEquals(403, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Échec de l\'inscription."}', $résultat_observé->getContent());
	}

	public function test_étant_donné_un_utilisateur_inexistant_avec_authentification_lorsquon_linscrit_il_est_sauvegardé_et_on_obtient_un_token()
	{
		putenv("AUTH_LOCAL=true");

		$marcel = new User("Marcel");

		$mockUserDAO = DAOFactory::getInstance()->get_user_dao();
		$mockUserDAO
			->shouldReceive("save")
			->once()
			->withArgs(function ($user) {
				return $user->username == "Marcel" && $user->rôle == User::ROLE_NORMAL;
			})
			->andReturn($marcel)
			->shouldReceive("set_password")
			->once()
			->withArgs(function ($user) {
				return $user->username == "Marcel";
			}, "password");

		$résultat_observé = $this->call("POST", "/inscription", ["username" => "Marcel", "password" => "test"]);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertEquals('{"Token":"token valide"}', $résultat_observé->getContent());
	}

	# Identifiants invalides

	public function test_étant_donné_une_authentificaton_locale_lorsquon_inscrit_un_nom_dutilisateur_vide_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");
		$résultat_observé = $this->call("POST", "/inscription", ["username" => "", "password" => "test"]);

		$this->assertEquals(400, $résultat_observé->status());
	}

	public function test_étant_donné_une_authentificaton_locale_lorsquon_inscrit_sans_nom_dutlisateur_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");
		$résultat_observé = $this->call("POST", "/inscription", ["password" => "test"]);

		$this->assertEquals(400, $résultat_observé->status());
	}

	public function test_étant_donné_une_authentification_locale_lorsquon_inscrit_sans_mot_de_passe_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");

		$résultat_observé = $this->call("POST", "/inscription", ["username" => "Marcel"]);

		$this->assertEquals(400, $résultat_observé->status());
	}

	public function test_étant_donné_une_authentification_locale_lorsquon_inscrit_avec_mot_de_passe_vide_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");

		$résultat_observé = $this->call("POST", "/inscription", ["username" => "Marcel", "password" => ""]);

		$this->assertEquals(400, $résultat_observé->status());
	}
}
