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

namespace progression\domaine\interacteur;

use progression\dao\DAOFactory;
use progression\domaine\entité\user\{User, État, Rôle};
use PHPUnit\Framework\TestCase;
use Carbon\Carbon;
use Mockery;

final class InscriptionIntTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		putenv("AUTH_LOCAL=true");
		putenv("APP_URL=https://example.com");
		putenv("PREFERENCES_DEFAUT=");
		putenv("JWT_SECRET=secret-test");
		putenv("JWT_EXPIRATION=15");
		putenv("APP_VERSION=2.0.0");

		$mockUserDao = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDao
			->shouldReceive("get_user")
			->with("bob")
			->andReturn(new User(username: "bob", date_inscription: 0, courriel: "bob@progressionmail.com"));
		$mockUserDao
			->shouldReceive("trouver")
			->with(null, "bob@progressionmail.com")
			->andReturn(new User(username: "bob", date_inscription: 0, courriel: "bob@progressionmail.com"));
		$mockUserDao->shouldReceive("get_user")->andReturn(null);

		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDao);

		$mockExpéditeurDao = Mockery::mock("progression\\dao\\mail\Expéditeur");
		$mockDAOFactory->shouldReceive("get_expéditeur")->andReturn($mockExpéditeurDao);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
		DAOFactory::setInstance(null);
	}

	public function test_étant_donné_un_utilisateur_non_existant_et_un_type_dauthentification_local_lorsquon_effectue_linscription_il_est_sauvegardé_et_on_reçoit_le_nouveau_User_en_attente()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=true");

		Carbon::setTestNow(Carbon::create(2001, 5, 21, 12));

		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->shouldReceive("get_user")
			->with("roger")
			->andReturn(
				null,
				new User(
					username: "roger",
					date_inscription: 0,
					courriel: "roger@gmail.com",
					état: État::ATTENTE_DE_VALIDATION,
				),
			);
		$mockUserDao
			->shouldReceive("trouver")
			->with(Mockery::any(), "roger@gmail.com")
			->andReturn(null);
		$mockUserDao
			->shouldReceive("save")
			->withArgs(function ($user) {
				return $user->username == "roger" &&
					$user->rôle == Rôle::NORMAL &&
					$user->état == État::ATTENTE_DE_VALIDATION;
			})
			->once()
			->andReturnArg(0);
		$mockUserDao
			->shouldReceive("set_password")
			->once()
			->withArgs(function ($user, $password) {
				return $user->username == "roger" && $password == "password";
			});

		$mockExpéditeurDao = DAOFactory::getInstance()->get_expéditeur();
		$mockExpéditeurDao
			->shouldReceive("envoyer_validation_courriel")
			->withArgs(function ($user, $token) {
				return $user->courriel == "roger@gmail.com" &&
					$token ==
						"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6InJvZ2VyIiwiY3VycmVudCI6OTkwNDQ2NDAwLCJleHBpcmVkIjo5OTA0NDczMDAsInJlc3NvdXJjZXMiOnsiZGF0YSI6eyJ1cmxfdXNlciI6Imh0dHBzOlwvXC9leGFtcGxlLmNvbVwvdXNlclwvcm9nZXIiLCJ1c2VyIjp7InVzZXJuYW1lIjoicm9nZXIiLCJjb3VycmllbCI6InJvZ2VyQGdtYWlsLmNvbSIsInJcdTAwZjRsZSI6MH19LCJwZXJtaXNzaW9ucyI6eyJ1c2VyIjp7InVybCI6Il51c2VyXC9yb2dlciQiLCJtZXRob2QiOiJeUE9TVCQifX19LCJ2ZXJzaW9uIjoiMi4wLjAifQ.Og0J_bP4xxypUvClVU1qLqDmHxB_Dfyk_2RiNxFYRMI";
			})
			->andReturn();

		$user = (new InscriptionInt())->effectuer_inscription_locale("roger", "roger@gmail.com", "password");

		$this->assertEquals(
			new User(
				username: "roger",
				date_inscription: 990446400,
				courriel: "roger@gmail.com",
				état: État::ATTENTE_DE_VALIDATION,
			),
			$user,
		);
	}

	public function test_étant_donné_un_utilisateur_non_existant_et_un_type_dauthentification_no_lorsquon_effectue_linscription_il_est_sauvegardé_et_on_reçoit_le_nouveau_User_actif()
	{
		putenv("AUTH_LOCAL=false");
		putenv("AUTH_LDAP=false");

		Carbon::setTestNow(Carbon::create(2001, 5, 21, 12));

		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->shouldReceive("get_user")
			->with("roger")
			->andReturn(null, new User(username: "roger", date_inscription: 0));
		$mockUserDao
			->shouldReceive("save")
			->withArgs(function ($user) {
				return $user->username == "roger" && $user->état == État::ACTIF;
			})
			->once()
			->andReturnArg(0);
		$mockUserDao->shouldNotReceive("set_password");

		$mockExpéditeurDao = DAOFactory::getInstance()->get_expéditeur();
		$mockExpéditeurDao->shouldNotReceive("envoyer_validation_courriel");

		$user = (new InscriptionInt())->effectuer_inscription_sans_mdp("roger");

		$this->assertEquals(
			new User("roger", courriel: null, date_inscription: 990446400, préférences: "", état: État::ACTIF),
			$user,
		);
	}

	public function test_étant_donné_un_utilisateur_non_existant_sans_authentification_et_une_variable_PREFERENCES_DEFAUT_définie_lorsquon_effectue_linscription_il_est_sauvegardé_avec_des_préférences_par_défaut()
	{
		putenv("AUTH_LOCAL=false");
		putenv("AUTH_LDAP=false");
		putenv("PREFERENCES_DEFAUT=préférences par défaut");

		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->allows()
			->get_user("roger")
			->andReturn(null, new User("roger", date_inscription: 990446400, préférences: "préférences par défaut"));
		$mockUserDao
			->shouldReceive("save")
			->withArgs(function ($user) {
				return $user->username == "roger" && $user->préférences == "préférences par défaut";
			})
			->once()
			->andReturnArg(0);

		$user = (new InscriptionInt())->effectuer_inscription_sans_mdp("roger");
		$this->assertEquals(
			new User("roger", date_inscription: 990446400, état: État::ACTIF, préférences: "préférences par défaut"),
			$user,
		);
	}

	public function test_étant_donné_un_utilisateur_non_existant_un_type_dauthentification_locale_et_une_variable_PREFERENCES_DEFAUT_définie_lorsquon_effectue_linscription_il_est_sauvegardé_avec_des_préférences_par_défaut()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");
		putenv("PREFERENCES_DEFAUT=préférences par défaut");

		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->allows()
			->get_user("roger")
			->andReturn(
				null,
				new User(
					"roger",
					courriel: "roger@testmail.com",
					date_inscription: 990446400,
					préférences: "préférences par défaut",
				),
			);
		$mockUserDao
			->shouldReceive("trouver")
			->with(Mockery::any(), "roger@testmail.com")
			->andReturn(null);
		$mockUserDao
			->shouldReceive("save")
			->withArgs(function ($user) {
				return $user->username == "roger" && $user->préférences == "préférences par défaut";
			})
			->once()
			->andReturnArg(0);
		$mockUserDao
			->shouldReceive("set_password")
			->once()
			->withArgs(function ($user, $password) {
				return $user->username == "roger" && $password == "password";
			});
		$mockExpéditeurDao = DAOFactory::getInstance()->get_expéditeur();
		$mockExpéditeurDao
			->shouldReceive("envoyer_validation_courriel")
			->withArgs(function ($user, $token) {
				return $user->courriel == "roger@testmail.com";
			})
			->andReturn();

		$user = (new InscriptionInt())->effectuer_inscription_locale("roger", "roger@testmail.com", "password");

		$this->assertEquals(
			new User(
				"roger",
				courriel: "roger@testmail.com",
				date_inscription: 990446400,
				état: État::ATTENTE_DE_VALIDATION,
				préférences: "préférences par défaut",
			),
			$user,
		);
	}

	public function test_étant_donné_un_utilisateur_non_existant_sans_authentification_et_une_variable_PREFERENCES_DEFAUT_non_définie_lorsquon_effectue_linscription_il_est_sauvegardé_sans_préférences()
	{
		putenv("AUTH_LOCAL=false");
		putenv("AUTH_LDAP=false");
		putenv("PREFERENCES_DEFAUT=");

		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->allows()
			->get_user("roger")
			->andReturn(null, new User("roger", date_inscription: 990446400, préférences: ""));
		$mockUserDao
			->shouldReceive("save")
			->withArgs(function ($user) {
				return $user->username == "roger" && $user->préférences == "";
			})
			->once()
			->andReturnArg(0);

		$user = (new InscriptionInt())->effectuer_inscription_sans_mdp("roger");

		$this->assertEquals(new User("roger", date_inscription: 990446400, préférences: "", état: État::ACTIF), $user);
	}

	public function test_étant_donné_un_utilisateur_non_existant_et_un_type_dauthentification_local_lorsquon_effectue_linscription_sans_mdp_on_obtient_null()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=true");

		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->shouldReceive("get_user")
			->with("roger")
			->andReturn(null, new User(username: "roger", date_inscription: 0));
		$mockUserDao->shouldNotReceive("save");
		$mockUserDao->shouldNotReceive("set_password");

		$user = (new InscriptionInt())->effectuer_inscription_locale("roger", "roger@gmail.com", null);

		$this->assertNull($user);
	}

	public function test_étant_donné_un_nouvel_admin_lorsquon_effectue_linscription_il_est_sauvegardé_et_on_reçoit_le_nouveau_User_actif()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		Carbon::setTestNow(Carbon::create(2001, 5, 21, 12));

		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->shouldReceive("get_user")
			->with("admin")
			->andReturn(
				null,
				new User(
					username: "admin",
					date_inscription: 0,
					courriel: "admin@gmail.com",
					état: État::ACTIF,
					rôle: Rôle::ADMIN,
				),
			);
		$mockUserDao
			->shouldReceive("trouver")
			->with(Mockery::any(), "admin@gmail.com")
			->andReturn(null);
		$mockUserDao
			->shouldReceive("save")
			->withArgs(function ($user) {
				return $user->username == "admin" && $user->état == État::ACTIF && $user->rôle == Rôle::ADMIN;
			})
			->once()
			->andReturnArg(0);
		$mockUserDao
			->shouldReceive("set_password")
			->once()
			->withArgs(function ($user, $password) {
				return $user->username == "admin" && $password == "password";
			});

		$user = (new InscriptionInt())->effectuer_inscription_locale(
			"admin",
			"admin@gmail.com",
			"password",
			rôle: Rôle::ADMIN,
		);

		$this->assertEquals(
			new User(
				username: "admin",
				date_inscription: 990446400,
				courriel: "admin@gmail.com",
				état: État::ACTIF,
				rôle: Rôle::ADMIN,
			),
			$user,
		);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_linscrit_avec_le_même_username_on_obtient_null()
	{
		$user = (new InscriptionInt())->effectuer_inscription_locale(
			"bob",
			courriel: "bob@test.com",
			password: "password",
		);

		$this->assertNull($user);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_linscrit_avec_le_même_courriel_on_obtient_null()
	{
		$user = (new InscriptionInt())->effectuer_inscription_locale(
			"autrenom",
			courriel: "bob@progressionmail.com",
			password: "password",
		);

		$this->assertNull($user);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_linscrit_avec_le_même_courriel_et_username_on_obtient_null()
	{
		$user = (new InscriptionInt())->effectuer_inscription_locale(
			"bob",
			courriel: "bob@progressionmail.com",
			password: "password",
		);

		$this->assertNull($user);
	}
}
