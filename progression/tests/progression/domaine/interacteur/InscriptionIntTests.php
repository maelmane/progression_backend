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
use progression\http\contrôleur\GénérateurDeToken;
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

		$mockUserDao = Mockery::mock("progression\\dao\\UserDAO");
		$mockExpéditeurDao = Mockery::mock("progression\\dao\\mail\Expéditeur");

		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDao);
		DAOFactory::setInstance($mockDAOFactory);

		$mockDAOFactory->shouldReceive("get_expéditeur")->andReturn($mockExpéditeurDao);
	}

	public function tearDown(): void
	{
		Mockery::close();
		DAOFactory::setInstance(null);
		GénérateurDeToken::set_instance(null);
	}

	public function test_étant_donné_un_utilisateur_non_existant_et_un_type_dauthentification_local_lorsquon_effectue_linscription_il_est_sauvegardé_et_on_reçoit_le_nouveau_User_en_attente()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=true");

		putenv("JWT_SECRET=secret-test");
		putenv("JWT_EXPIRATION=15");

		Carbon::setTestNowAndTimezone(Carbon::create(2001, 5, 21, 12));

		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->shouldReceive("get_user")
			->with("roger")
			->andReturn(null, new User("roger", "roger@gmail.com", État::ATTENTE_DE_VALIDATION));
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
			->andReturn(new User("roger", "roger@gmail.com", État::ATTENTE_DE_VALIDATION));
		$mockUserDao
			->shouldReceive("set_password")
			->once()
			->withArgs(function ($user, $password) {
				return $user->username == "roger" && $password == "password";
			});

		$mockGénérateurDeToken = Mockery::mock("progression\\http\\contrôleur\\GénérateurDeToken");
		GénérateurDeToken::set_instance($mockGénérateurDeToken);

		$mockGénérateurDeToken
			->shouldReceive("générer_token")
			->once()
			->withArgs(function ($username, $expiration, $ressources) {
				$ressources_attendues = [
					"data" => [
						"url_user" => "https://example.com/user/roger",
						"user" => [
							"username" => "roger",
							"courriel" => "roger@gmail.com",
							"rôle" => Rôle::NORMAL,
						],
					],
					"permissions" => [
						"user" => [
							"url" => "^user/roger$",
							"method" => "^POST$",
						],
					],
				];

				return $username == "roger" && $expiration == 990447300 && $ressources == $ressources_attendues;
			})
			->andReturn("token valide");

		$mockExpéditeurDao = DAOFactory::getInstance()->get_expéditeur();
		$mockExpéditeurDao
			->shouldReceive("envoyer_validation_courriel")
			->withArgs(function ($user, $token) {
				return $user->courriel == "roger@gmail.com" && $token == "token valide";
			})
			->andReturn();

		$user = (new InscriptionInt())->effectuer_inscription_locale("roger", "roger@gmail.com", "password");

		$this->assertEquals(new User("roger", "roger@gmail.com", État::ATTENTE_DE_VALIDATION), $user);
	}

	public function test_étant_donné_un_utilisateur_non_existant_et_un_type_dauthentification_no_lorsquon_effectue_linscription_il_est_sauvegardé_et_on_reçoit_le_nouveau_User_actif()
	{
		putenv("AUTH_LOCAL=false");
		putenv("AUTH_LDAP=false");

		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->shouldReceive("get_user")
			->with("roger")
			->andReturn(null, new User("roger"));
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

		$this->assertEquals(new User("roger", état: État::ACTIF), $user);
	}

	public function test_étant_donné_un_utilisateur_non_existant_et_un_type_dauthentification_local_lorsquon_effectue_linscription_sans_mdp_on_obtient_null()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=true");

		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->shouldReceive("get_user")
			->with("roger")
			->andReturn(null, new User("roger"));
		$mockUserDao->shouldNotReceive("save");
		$mockUserDao->shouldNotReceive("set_password");

		$user = (new InscriptionInt())->effectuer_inscription_locale("roger", "roger@gmail.com", null);

		$this->assertNull($user);
	}

	public function test_étant_donné_un_nouvel_admin_lorsquon_effectue_linscription_il_est_sauvegardé_et_on_reçoit_le_nouveau_User_actif()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		putenv("JWT_SECRET=secret-test");

		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->shouldReceive("get_user")
			->with("admin")
			->andReturn(null, new User("admin", "admin@gmail.com", État::ACTIF, Rôle::ADMIN));
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

		$this->assertEquals(new User("admin", "admin@gmail.com", État::ACTIF, Rôle::ADMIN), $user);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_linscrit_avec_le_même_username_on_obtient_null()
	{
		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->shouldReceive("get_user")
			->with("bob")
			->andReturn(new User("bob", "bob@progressionmail.com"));

		$user = (new InscriptionInt())->effectuer_inscription_locale(
			"bob",
			courriel: "bob@test.com",
			password: "password",
		);

		$this->assertNull($user);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_linscrit_avec_le_même_courriel_on_obtient_null()
	{
		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->shouldReceive("get_user")
			->with("bob")
			->andReturn(new User("bob", "bob@progressionmail.com"));
		$mockUserDao
			->shouldReceive("trouver")
			->with(null, "bob@progressionmail.com")
			->andReturn(new User("bob", "bob@progressionmail.com"));
		$mockUserDao->shouldReceive("get_user")->andReturn(null);

		$user = (new InscriptionInt())->effectuer_inscription_locale(
			"autrenom",
			courriel: "bob@progressionmail.com",
			password: "password",
		);

		$this->assertNull($user);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_linscrit_avec_le_même_courriel_et_username_on_obtient_null()
	{
		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->shouldReceive("get_user")
			->with("bob")
			->andReturn(new User("bob", "bob@progressionmail.com"));

		$user = (new InscriptionInt())->effectuer_inscription_locale(
			"bob",
			courriel: "bob@progressionmail.com",
			password: "password",
		);

		$this->assertNull($user);
	}
}
