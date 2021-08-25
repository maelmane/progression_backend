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

		$mockUserDao = Mockery::mock("progression\dao\UserDAO");

		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
		$mockDAOFactory
			->allows()
			->get_user_dao()
			->andReturn($mockUserDao);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_un_nouvel_utilisateur_lorsquon_effectue_linscription_il_est_sauvegardé_et_on_reçoit_le_nouveau_User()
	{
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

		$this->assertEquals($user, new User("roger"));
	}

	public function test_étant_donné_un_nouvel_admin_lorsquon_effectue_linscription_il_est_sauvegardé_et_on_reçoit_le_nouveau_User()
	{
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

		$this->assertEquals($user, new User("admin", User::ROLE_ADMIN));
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
