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

namespace progression\dao;

use progression\domaine\entité\TentativeProg;
use progression\dao\TentativeDAO;
use PHPUnit\Framework\TestCase;

final class TentativeDAOTests extends TestCase
{
	public function setUp(): void
	{
		EntitéDAO::get_connexion()->begin_transaction();
	}

	public function tearDown(): void
	{
		EntitéDAO::get_connexion()->rollback();
	}

	public function test_étant_donné_une_TentativeProg_existante_lorsquon_récupère_la_tentative_on_obtient_une_tentative_de_type_prog()
	{
		$résponse_observée = (new TentativeDAO(new DAOFactory()))->get_tentative(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
			1615696276,
		);

		$this->assertInstanceOf(TentativeProg::class, $résponse_observée);
	}

	public function test_étant_donné_une_tentative_inexistante_lorsquon_récupère_la_tentative_on_obtient_null()
	{
		$réponse_attendue = null;

		$résponse_observée = (new TentativeDAO(new DAOFactory()))->get_tentative("exemple", "exemple", 0);

		$this->assertEquals($réponse_attendue, $résponse_observée);
	}
}
