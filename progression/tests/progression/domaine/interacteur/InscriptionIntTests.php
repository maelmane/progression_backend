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
use progression\domaine\entité\User;
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

	public function test_étant_donné_un_utilisateur_non_existant_et_un_type_dauthentification_local_lorsquon_effectue_linscription_il_est_sauvegardé_et_on_reçoit_le_nouveau_User()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=true");

		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->allows()
			->get_user("roger")
			->andReturn(null, new User("roger"));
		$mockUserDao
			->shouldReceive("save")
			->withArgs(function ($user) {
				return $user->username == "roger" && $user->rôle == User::ROLE_NORMAL;
			})
			->once()
			->andReturn(new User("roger"));
		$mockUserDao
			->shouldReceive("set_password")
			->once()
			->withArgs(function ($user, $password) {
				return $user->username == "roger" && $password == "password";
			});

		$user = (new InscriptionInt())->effectuer_inscription("roger", "password");

		$this->assertEquals(new User("roger"), $user);
	}

	public function test_étant_donné_un_utilisateur_non_existant_et_un_type_dauthentification_no_lorsquon_effectue_linscription_il_est_sauvegardé_et_on_reçoit_le_nouveau_User()
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
				return $user->username == "roger" && $user->rôle == User::ROLE_NORMAL;
			})
			->once()
			->andReturn(new User("roger"));
		$mockUserDao->shouldNotReceive("set_password");

		$user = (new InscriptionInt())->effectuer_inscription("roger");

		$this->assertEquals(new User("roger"), $user);
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
			->andReturn(null, new User("roger", préférences: "préférences par défaut"));
		$mockUserDao
			->shouldReceive("save")
			->withArgs(function ($user) {
				return $user->username == "roger" && $user->préférences == "préférences par défaut";
			})
			->once()
			->andReturn(new User("roger", préférences: "préférences par défaut"));

		$user = (new InscriptionInt())->effectuer_inscription("roger");

		$this->assertEquals(new User("roger", préférences: "préférences par défaut"), $user);
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
			->andReturn(null, new User("roger", préférences: "préférences par défaut"));
		$mockUserDao
			->shouldReceive("save")
			->withArgs(function ($user) {
				return $user->username == "roger" && $user->préférences == "préférences par défaut";
			})
			->once()
			->andReturn(new User("roger", préférences: "préférences par défaut"));
		$mockUserDao
			->shouldReceive("set_password")
			->once()
			->withArgs(function ($user, $password) {
				return $user->username == "roger" && $password == "password";
			});

		$user = (new InscriptionInt())->effectuer_inscription("roger", "password");

		$this->assertEquals(new User("roger", préférences: "préférences par défaut"), $user);
	}

	public function test_étant_donné_un_utilisateur_non_existant_et_une_variable_PREFERENCES_DEFAUT_non_définie_lorsquon_effectue_linscription_il_est_sauvegardé_sans_préférences()
	{
		putenv("AUTH_LOCAL=false");
		putenv("AUTH_LDAP=false");
		putenv("PREFERENCES_DEFAUT=");

		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->allows()
			->get_user("roger")
			->andReturn(null, new User("roger", préférences: ""));
		$mockUserDao
			->shouldReceive("save")
			->withArgs(function ($user) {
				return $user->username == "roger" && $user->préférences == "";
			})
			->once()
			->andReturn(new User("roger", préférences: ""));

		$user = (new InscriptionInt())->effectuer_inscription("roger");

		$this->assertEquals(new User("roger", préférences: ""), $user);
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

		$user = (new InscriptionInt())->effectuer_inscription("roger");

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

	public function test_étant_donné_un_nouvel_admin_lorsquon_effectue_linscription_il_est_sauvegardé_et_on_reçoit_le_nouveau_User()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->allows()
			->get_user("admin")
			->andReturn(null, new User("admin", User::ROLE_ADMIN));
		$mockUserDao
			->shouldReceive("save")
			->withArgs(function ($user) {
				return $user->username == "admin" && $user->rôle == User::ROLE_ADMIN;
			})
			->once()
			->andReturn(new User("admin", User::ROLE_ADMIN));
		$mockUserDao
			->shouldReceive("set_password")
			->once()
			->withArgs(function ($user, $password) {
				return $user->username == "admin" && $password == "password";
			});

		$user = (new InscriptionInt())->effectuer_inscription("admin", "password", User::ROLE_ADMIN);

		$this->assertEquals(new User("admin", User::ROLE_ADMIN), $user);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_effectue_à_nouveau_linscription_on_obtient_null()
	{
		$mockUserDao = DAOFactory::getInstance()->get_user_dao();
		$mockUserDao
			->allows()
			->get_user("bob")
			->andReturn(new User("bob"));

		$user = (new InscriptionInt())->effectuer_inscription("bob", "password");

		$this->assertNull($user);
	}
}
