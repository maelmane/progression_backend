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
use progression\domaine\entité\{User};
use PHPUnit\Framework\TestCase;
use Mockery;

final class CréerUserIntTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		$mockUserDao = Mockery::mock("progression\dao\UserDAO");
		$mockUserDao
			->allows()
			->get_user("jdoe")
			->andReturn(new User("jdoe"));
		$mockUserDao
			->allows()
			->get_user("bob")
			->andReturn(null);

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
		DAOFactory::setInstance(null);
	}

	public function test_étant_donné_un_nom_dutilisateur_valide_lorsquon_crée_lutilisateur_il_est_sauvegardé()
	{
		$mockUserDao = DAOFactory::getInstance()->get_user_dao();

		$mockUserDao
			->shouldReceive("save")
			->withArgs(function ($user) {
				return $user->username == "bob" && $user->rôle == USER::ROLE_NORMAL;
			})
			->andReturnArg(0);

		$userInt = new CréerUserInt();
		$résultat_obtenu = $userInt->créer_user("bob");

		$this->assertEquals(new User("bob"), $résultat_obtenu);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_le_crée_de_nouveau_on_obtient_null()
	{
		$userInt = new CréerUserInt();
		$résultat_obtenu = $userInt->créer_user("jdoe");

		$this->assertNull($résultat_obtenu);
	}

	public function test_étant_donné_un_nom_dutilisateur_invalide_lorsquon_crée_lutilisateur_on_obtient_null()
	{
		$userInt = new CréerUserInt();
		$résultat_obtenu = $userInt->créer_user("");

		$this->assertNull($résultat_obtenu);
	}
}
