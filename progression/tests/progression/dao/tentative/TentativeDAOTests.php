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

namespace progression\dao\tentative;

use progression\TestCase;
use progression\dao\EntitéDAO;

final class TentativeDAOTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();
		EntitéDAO::get_connexion()->begin_transaction();
	}

	public function tearDown(): void
	{
		parent::tearDown();
		EntitéDAO::get_connexion()->rollback();
	}

	public function test_étant_donné_une_tentative_inexistante_lorsquon_récupère_la_tentative_on_obtient_null()
	{
		$résultat_observé = (new TentativeDAO())->get_tentative("exemple", "exemple", 0);

		$this->assertNull($résultat_observé);
	}

	public function test_étant_donné_une_question_inexistante_lorsquon_récupère_toutes_les_tentatives_on_obtient_un_tableau_vide()
	{
		$résultat_observé = (new TentativeDAO())->get_toutes("exemple", "exemple");

		$this->assertEquals([], $résultat_observé);
	}
}
