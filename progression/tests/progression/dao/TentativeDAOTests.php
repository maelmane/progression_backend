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

	public function test_étant_donné_une_TentativeProg_non_réussie_lorsquon_récupère_la_tentative_on_obtient_une_tentative_de_type_prog()
	{
		$résultat_attendu = new TentativeProg("python", "print(\"Tourlou le monde!\")", 1615696276, false);

		$résultat_observé = (new TentativeDAO(new DAOFactory()))->get_tentative(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
			1615696276,
		);

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}

	public function test_étant_donné_une_TentativeProg_réussie_lorsquon_récupère_la_tentative_on_obtient_une_tentative_de_type_prog()
	{
		$résultat_attendu = new TentativeProg("python", "print(\"Allo tout le monde!\")", 1615696296, true);

		$résultat_observé = (new TentativeDAO(new DAOFactory()))->get_tentative(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction",
			1615696296,
		);

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}

	public function test_étant_donné_une_tentative_inexistante_lorsquon_récupère_la_tentative_on_obtient_null()
	{
		$résultat_attendu = null;

		$résultat_observé = (new TentativeDAO(new DAOFactory()))->get_tentative("exemple", "exemple", 0);

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}
}
