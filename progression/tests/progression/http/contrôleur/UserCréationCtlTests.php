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
use progression\domaine\entité\user\{User, État, Rôle};
use progression\dao\DAOFactory;
use Illuminate\Auth\GenericUser;
use Carbon\Carbon;

final class UserCréationCtlTests extends ContrôleurTestCase
{
	public $user;

	public function setUp(): void
	{
		parent::setUp();

		putenv("AUTH_LDAP=false");

		putenv("APP_URL=https://example.com");
		putenv("JWT_SECRET=secret-test");

		$this->user = new GenericUser([
			"username" => "bob",
			"rôle" => Rôle::NORMAL,
			"état" => État::ACTIF,
		]);

		// UserDAO
		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("bob")
			->andReturn(
				new User(
					username: "bob",
					date_inscription: 1590828610,
					courriel: "bob@progressionmail.com",
					état: État::ACTIF,
				),
			);
		$mockUserDAO
			->shouldReceive("get_user")
			->with("nouveau")
			->andReturn(
				new User(
					username: "nouveau",
					date_inscription: 1610828612,
					courriel: "nouveau@mail.com",
					état: État::ATTENTE_DE_VALIDATION,
				),
			);
		$mockUserDAO
			->shouldReceive("get_user")
			->with("Marcel")
			->andReturn(null);
		$mockUserDAO
			->shouldReceive("get_user")
			->with("Marcel2")
			->andReturn(null);
		$mockUserDAO
			->shouldReceive("get_user")
			->with("johnny")
			->andReturn(null);

		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		DAOFactory::setInstance($mockDAOFactory);

		$mockExpéditeurDao = Mockery::mock("progression\\dao\\mail\Expéditeur");
		$mockDAOFactory
			->allows()
			->get_expéditeur()
			->andReturn($mockExpéditeurDao);
	}

	public function tearDown(): void
	{
		Mockery::close();
	}

	#  AUTH_LOCAL = false
	public function test_étant_donné_un_utilisateur_existant_sans_authentification_lorsquon_inscrit_de_nouveau_on_obtient_un_user()
	{
		putenv("AUTH_LOCAL=false");

		$résultat_observé = $this->call("PUT", "/user", ["username" => "bob"]);

		$mockExpéditeurDao = DAOFactory::getInstance()->get_expéditeur();
		$mockExpéditeurDao->shouldNotReceive("envoyer_validation_courriel");

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/userCréationCtlTest_user_existant_sans_auth.json",
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_utilisateur_inexistant_sans_authentification_lorsquon_linscrit_il_est_sauvegardé_et_on_obtient_un_user()
	{
		putenv("AUTH_LOCAL=false");

		Carbon::setTestNow(Carbon::create(2021, 01, 16, 15, 23, 32));

		$mockUserDAO = DAOFactory::getInstance()->get_user_dao();
		$mockUserDAO
			->shouldReceive("save")
			->once()
			->withArgs(function ($user) {
				return $user->username == "Marcel";
			})
			->andReturnArg(0);

		$mockExpéditeurDao = DAOFactory::getInstance()->get_expéditeur();
		$mockExpéditeurDao->shouldNotReceive("envoyer_validation_courriel");

		$résultat_observé = $this->call("PUT", "/user", ["username" => "Marcel"]);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/userCréationCtlTest_user_inexistant_sans_auth.json",
			$résultat_observé->getContent(),
		);
	}

	# AUTH_LOCAL = true
	public function test_étant_donné_un_utilisateur_existant_avec_authentification_lorsquon_linscrit_de_nouveau_avec_un_username_existant_on_obtient_une_erreur_409()
	{
		putenv("AUTH_LOCAL=true");

		$mockExpéditeurDao = DAOFactory::getInstance()->get_expéditeur();
		$mockExpéditeurDao->shouldNotReceive("envoyer_validation_courriel");

		$résultat_observé = $this->call("PUT", "/user", [
			"username" => "bob",
			"courriel" => "zozo@gmail.com",
			"password" => "Test1234",
		]);

		$this->assertEquals(409, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"username":["Err: 1001. Le nom d\'utilisateur existe déjà."]}}',
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_utilisateur_inexistant_avec_authentification_lorsquon_linscrit_avec_un_courriel_existant_on_obtient_une_erreur_409()
	{
		putenv("AUTH_LOCAL=true");

		$mockExpéditeurDao = DAOFactory::getInstance()->get_expéditeur();
		$mockExpéditeurDao->shouldNotReceive("envoyer_validation_courriel");

		$résultat_observé = $this->call("PUT", "/user", [
			"username" => "johnny",
			"courriel" => "jane@gmail.com",
			"password" => "Test1234",
		]);

		$this->assertEquals(409, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"courriel":["Err: 1001. Le courriel existe déjà."]}}',
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_utilisateur_inexistant_avec_authentification_lorsquon_linscrit_avec_un_courriel_invalide_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");

		$mockExpéditeurDao = DAOFactory::getInstance()->get_expéditeur();
		$mockExpéditeurDao->shouldNotReceive("envoyer_validation_courriel");

		$résultat_observé = $this->call("PUT", "/user", [
			"username" => "bobby",
			"courriel" => "bobby.com",
			"password" => "Test1234",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"courriel":["Err: 1003. Le champ courriel doit être un courriel valide."]}}',
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_utilisateur_inexistant_avec_authentification_lorsquon_linscrit_avec_un_password_invalide_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");

		$mockExpéditeurDao = DAOFactory::getInstance()->get_expéditeur();
		$mockExpéditeurDao->shouldNotReceive("envoyer_validation_courriel");

		$résultat_observé = $this->call("PUT", "/user", [
			"username" => "bobby",
			"courriel" => "bobby@gmail.com",
			"password" => "pasbon",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"password":["Err: 1003. Le mot de passe doit contenir au moins 8 caractères, une majuscule et un chiffre."]}}',
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_utilisateur_existant_avec_authentification_lorsquon_linscrit_de_nouveau_avec_une_casse_différente_on_obtient_une_erreur_409()
	{
		putenv("AUTH_LOCAL=true");

		$mockExpéditeurDao = DAOFactory::getInstance()->get_expéditeur();
		$mockExpéditeurDao->shouldNotReceive("envoyer_validation_courriel");

		$résultat_observé = $this->call("PUT", "/user", [
			"username" => "BOB",
			"courriel" => "bob@gmail.com",
			"password" => "Test1234",
		]);
		$this->assertEquals(409, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"username":["Err: 1001. Le nom d\'utilisateur existe déjà."]}}',
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_utilisateur_inexistant_avec_authentification_lorsquon_linscrit_il_est_sauvegardé_et_on_obtient_un_user()
	{
		putenv("AUTH_LOCAL=true");

		Carbon::setTestNow(Carbon::create(2021, 01, 16, 15, 23, 32));

		$mockUserDAO = DAOFactory::getInstance()->get_user_dao();
		$mockUserDAO
			->shouldReceive("trouver")
			->with(Mockery::any(), "marcel2@gmail.com")
			->andReturn(null);
		$mockUserDAO
			->shouldReceive("save")
			->once()
			->withArgs(function ($user) {
				return $user->username == "Marcel2";
			})
			->andReturnArg(0)
			->shouldReceive("set_password")
			->once()
			->withArgs(function ($user) {
				return $user->username == "Marcel2";
			}, "password");

		$mockExpéditeurDao = DAOFactory::getInstance()->get_expéditeur();
		$mockExpéditeurDao->shouldReceive("envoyer_validation_courriel")->once();

		$résultat_observé = $this->call("PUT", "/user", [
			"username" => "Marcel2",
			"courriel" => "marcel2@gmail.com",
			"password" => "Test1234",
		]);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/userCréationCtlTest_user_inexistant_avec_auth.json",
			$résultat_observé->getContent(),
		);
	}

	# Demande de renvoie de courriel de validation
	public function test_étant_donné_un_utilisateur_en_attente_de_validation_lorsquon_linscrit_de_nouveau_sans_mdp_il_nest_pas_sauvegardé_et_un_courriel_de_validation_est_renvoyé()
	{
		putenv("AUTH_LOCAL=true");

		$mockUserDAO = DAOFactory::getInstance()->get_user_dao();
		$mockUserDAO->shouldNotReceive("save")->shouldNotReceive("set_password");

		$mockExpéditeurDao = DAOFactory::getInstance()->get_expéditeur();
		$mockExpéditeurDao->shouldReceive("envoyer_validation_courriel")->once();

		$résultat_observé = $this->call("PUT", "/user", [
			"username" => "nouveau",
			"courriel" => "nouveau@progressionmail.com",
		]);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/userCréationCtlTest_renvoi_courriel.json",
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_utilisateur_actif_lorsquon_linscrit_de_nouveau_sans_mdp_le_courriel_de_validation_nest_pas_renvoyé_et_on_obtient_une_erreur_403()
	{
		putenv("AUTH_LOCAL=true");

		$mockUserDAO = DAOFactory::getInstance()->get_user_dao();
		$mockUserDAO->shouldNotReceive("save")->shouldNotReceive("set_password");

		$mockExpéditeurDao = DAOFactory::getInstance()->get_expéditeur();
		$mockExpéditeurDao->shouldNotReceive("envoyer_validation_courriel");

		$résultat_observé = $this->call("PUT", "/user", [
			"username" => "bob",
			"courriel" => "bob@progressionmail.com",
		]);

		$this->assertEquals(403, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Opération interdite."}', $résultat_observé->getContent());
	}

	# Identifiants invalides
	public function test_étant_donné_une_authentificaton_locale_lorsquon_inscrit_un_nom_dutilisateur_invalide_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");

		$mockExpéditeurDao = DAOFactory::getInstance()->get_expéditeur();
		$mockExpéditeurDao->shouldNotReceive("envoyer_validation_courriel");

		$résultat_observé = $this->call("PUT", "/user", [
			"username" => "B@B",
			"courriel" => "test@progressionmail.com",
			"password" => "Test01234",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"username":["Err: 1003. Le nom d\'utilisateur doit être composé de 2 à 64 caractères alphanumériques."]}}',
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_une_authentificaton_locale_lorsquon_inscrit_sans_nom_dutlisateur_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");

		$mockExpéditeurDao = DAOFactory::getInstance()->get_expéditeur();
		$mockExpéditeurDao->shouldNotReceive("envoyer_validation_courriel");

		$résultat_observé = $this->call("PUT", "/user", [
			"courriel" => "test@progressionmail.com",
			"password" => "Test01234",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"username":["Err: 1004. Le champ username est obligatoire."]}}',
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_une_authentification_locale_lorsquon_inscrit_un_nouvel_utilisateur_sans_mot_de_passe_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");

		$mockExpéditeurDao = DAOFactory::getInstance()->get_expéditeur();
		$mockExpéditeurDao->shouldNotReceive("envoyer_validation_courriel");

		$résultat_observé = $this->call("PUT", "/user", [
			"username" => "Marcel",
			"courriel" => "marcel@gmail.com",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"password":["Err: 1004. Le champ password est obligatoire."]}}',
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_une_authentification_locale_lorsquon_inscrit_un_nouvel_utilisateur_avec_mot_de_passe_vide_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");

		$mockExpéditeurDao = DAOFactory::getInstance()->get_expéditeur();
		$mockExpéditeurDao->shouldNotReceive("envoyer_validation_courriel");

		$résultat_observé = $this->call("PUT", "/user", [
			"username" => "Marcel",
			"courriel" => "marcel@gmail.com",
			"password" => "",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"password":["Err: 1004. Le champ password est obligatoire."]}}',
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_une_authentification_locale_lorsquon_inscrit_sans_userame_courriel_ni_mdp_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");

		$mockExpéditeurDao = DAOFactory::getInstance()->get_expéditeur();
		$mockExpéditeurDao->shouldNotReceive("envoyer_validation_courriel");

		$résultat_observé = $this->call("PUT", "/user", []);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"username":["Err: 1004. Le champ username est obligatoire."],"courriel":["Err: 1004. Le champ courriel est obligatoire."],"password":["Err: 1004. Le champ password est obligatoire."]}}',
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_une_authentification_locale_lorsquon_inscrit_sans_courriel_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");

		$mockExpéditeurDao = DAOFactory::getInstance()->get_expéditeur();
		$mockExpéditeurDao->shouldNotReceive("envoyer_validation_courriel");

		$résultat_observé = $this->call("PUT", "/user", ["username" => "Marcel", "password" => "Test1234"]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"courriel":["Err: 1004. Le champ courriel est obligatoire."]}}',
			$résultat_observé->getContent(),
		);
	}
}
