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
use Mockery;

final class InscriptionIntTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		putenv("AUTH_LOCAL=true");
		$mockUserDao = Mockery::mock("progression\\dao\\UserDAO");

		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory
			->allows()
			->get_user_dao()
			->andReturn($mockUserDao);
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

		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->allows()
			->get_user("roger")
			->andReturn(null, new User("roger", "roger@gmail.com", État::ATTENTE_DE_VALIDATION));
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

		$user = (new InscriptionInt())->effectuer_inscription("roger", "roger@gmail.com", "password");

		$this->assertEquals(new User("roger", "roger@gmail.com", État::ATTENTE_DE_VALIDATION), $user);
	}

	public function test_étant_donné_un_utilisateur_non_existant_et_un_type_dauthentification_no_lorsquon_effectue_linscription_il_est_sauvegardé_et_on_reçoit_le_nouveau_User_actif()
	{
		putenv("AUTH_LOCAL=false");
		putenv("AUTH_LDAP=false");

		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->allows()
			->get_user("roger")
			->andReturn(null, new User("roger"));
		$mockUserDao
			->shouldReceive("save")
			->withArgs(function ($user) {
				return $user->username == "roger" && $user->état == État::ACTIF;
			})
			->once()
			->andReturnArg(0);
		$mockUserDao->shouldNotReceive("set_password");

		$user = (new InscriptionInt())->effectuer_inscription("roger");

		$this->assertEquals(new User("roger", état: État::ACTIF), $user);
	}

	public function test_étant_donné_un_utilisateur_non_existant_et_un_type_dauthentification_local_lorsquon_effectue_linscription_sans_mdp_on_obtient_null()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=true");

		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->allows()
			->get_user("roger")
			->andReturn(null, new User("roger"));
		$mockUserDao->shouldNotReceive("save");
		$mockUserDao->shouldNotReceive("set_password");

		$user = (new InscriptionInt())->effectuer_inscription("roger", "roger@gmail.com");

		$this->assertNull($user);
	}

	public function test_étant_donné_un_utilisateur_non_existant_et_un_type_dauthentification_ldap_lorsquon_effectue_linscription_on_obtient_null()
	{
		putenv("AUTH_LOCAL=false");
		putenv("AUTH_LDAP=true");

		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->allows()
			->get_user("roger")
			->andReturn(null, new User("roger"));
		$mockUserDao->shouldNotReceive("save");
		$mockUserDao->shouldNotReceive("set_password");

		$user = (new InscriptionInt())->effectuer_inscription("roger");

		$this->assertNull($user);
	}

	public function test_étant_donné_un_nouvel_admin_lorsquon_effectue_linscription_il_est_sauvegardé_et_on_reçoit_le_nouveau_User_actif()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->allows()
			->get_user("admin")
			->andReturn(null, new User("admin", "admin@gmail.com", État::ACTIF, Rôle::ADMIN));
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

		$user = (new InscriptionInt())->effectuer_inscription(
			"admin",
			"admin@gmail.com",
			"password",
			rôle: Rôle::ADMIN,
		);

		$this->assertEquals(new User("admin", "admin@gmail.com", État::ACTIF, Rôle::ADMIN), $user);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_effectue_à_nouveau_linscription_on_obtient_null()
	{
		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->allows()
			->get_user("bob")
			->andReturn(new User("bob"));

		$user = (new InscriptionInt())->effectuer_inscription("bob", courriel: null, password: "password");

		$this->assertNull($user);
	}
}
