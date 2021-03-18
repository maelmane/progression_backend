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
use progression\dao\TentativeProgDAO;
use PHPUnit\Framework\TestCase;

final class TentativeProgDAOTests extends TestCase
{
	public function setUp(): void
	{
		EntitéDAO::get_connexion()->begin_transaction();
	}

	public function tearDown(): void
	{
		EntitéDAO::get_connexion()->rollback();
	}

	public function test_étant_donné_une_tentative_existante_lorsquon_récupère_la_tentative_on_obtient_la_tentative_correspondant()
	{
		$réponse_attendue = new TentativeProg("python", "print(\"Tourlou le monde!\")", 1615696276, 0);

		$résponse_observée = (new TentativeProgDAO())->get_tentative(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
			1615696276,
		);

		$this->assertEquals($réponse_attendue, $résponse_observée);
	}

	public function test_étant_donné_une_tentative_inexistante_lorsquon_récupère_la_tentative_on_obtient_null()
	{
		$réponse_attendue = null;

		$résponse_observée = (new TentativeProgDAO())->get_tentative("exemple", "exemple", 0);

		$this->assertEquals($réponse_attendue, $résponse_observée);
	}

	public function test_étant_donné_une_TentativeProg_lorsquon_récupère_toutes_les_tentatives_on_obtient_un_tableau_de_tentatives()
	{
		$réponse_attendue = [
			new TentativeProg("python", "print(\"Allo le monde!\")", 1615696286, 0),
			new TentativeProg("python", "print(\"Allo tout le monde!\")", 1615696296, 1),
		];

		$résponse_observée = (new TentativeProgDAO())->get_toutes(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction",
		);

		$this->assertEquals($réponse_attendue, $résponse_observée);
	}

	public function test_étant_donné_une_TentativeProg_lorsquon_sauvegarde_la_tentative_on_obtient_une_nouvelle_insertion_dans_la_table_reponse_prog()
	{
		$tentative_test = new TentativeProg("python", "testCode", 123456789);

		$réponse_attendue = new TentativeProg("python", "testCode", 123456789);

		$résponse_observée = (new TentativeProgDAO())->save($tentative_test, "Stefany", "https://exemple.com");
		$this->assertEquals($réponse_attendue, $résponse_observée);

		$résponse_observée = (new TentativeProgDAO())->get_tentative("Stefany", "https://exemple.com", 123456789);
		$this->assertEquals($réponse_attendue, $résponse_observée);
	}
}
