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

use progression\domaine\entité\User;
use progression\dao\DAOFactory;
use PHPUnit\Framework\TestCase;
use Mockery;

final class ObtenirUserIntTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		$mockUserDao = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDao
			->allows()
			->get_user("Bob", [])
			->andReturn(new User("Bob"));

		$mockUserDao->shouldReceive("get_user")->andReturn(null);

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

	public function test_étant_donné_un_utilisateur_Bob_lorsquon_le_cherche_par_username_on_obtient_un_objet_user_nommé_Bob()
	{
		$interacteur = new ObtenirUserInt();
		$résultat_obtenu = $interacteur->get_user("Bob");

		$résultat_attendu = new User("Bob");
		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}

	public function test_étant_donné_un_utilisateur_Banane_inexistant_lorsquon_le_cherche_par_username_on_obtient_null()
	{
		$interacteur = new ObtenirUserInt();
		$résultat_obtenu = $interacteur->get_user("Banane");

		$this->assertNull($résultat_obtenu);
	}
}
