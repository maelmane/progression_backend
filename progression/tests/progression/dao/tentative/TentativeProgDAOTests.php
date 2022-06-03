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

use progression\domaine\entité\{TentativeProg, Résultat};
use progression\TestCase;
use progression\dao\{DAOException, DAOFactory};
use progression\dao\EntitéDAO;

final class TentativeProgDAOTests extends TestCase
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

	public function test_étant_donné_une_TentativeProg_non_réussie_lorsquon_récupère_la_tentative_on_obtient_une_tentative_de_type_prog()
	{
		$résultat_attendu = new TentativeProg("python", "print(\"Tourlou le monde!\")", 1615696276, false, [], 2, 3456);
		$résultat_observé = (new TentativeDAO())->get_tentative(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
			1615696276,
		);

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}

	public function test_étant_donné_une_TentativeProg_réussie_lorsquon_récupère_la_tentative_on_obtient_une_tentative_de_type_prog()
	{
		$résultat_attendu = new TentativeProg(
			"python",
			"print(\"Allo tout le monde!\")",
			1615696296,
			true,
			[],
			4,
			345633,
		);

		$résultat_observé = (new TentativeDAO())->get_tentative(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction",
			1615696296,
		);

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}

	public function test_étant_donné_une_TentativeProg_lorsquon_récupère_toutes_les_tentatives_on_obtient_un_tableau_de_tentatives()
	{
		$résultat_attendue = [
			1615696286 => new TentativeProg("python", "print(\"Allo le monde!\")", 1615696286, false, [], 3, 34567),
			1615696296 => new TentativeProg(
				"python",
				"print(\"Allo tout le monde!\")",
				1615696296,
				true,
				[],
				4,
				345633,
			),
		];

		$résultat_observé = (new TentativeDAO())->get_toutes(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction",
		);

		$this->assertEquals($résultat_attendue, $résultat_observé);
	}

	public function test_étant_donné_une_TentativeProg_lorsquon_sauvegarde_la_tentative_on_obtient_une_nouvelle_insertion_dans_la_table_reponse_prog()
	{
		$tentative_test = new TentativeProg(
			"python",
			"testCode",
			123456789,
			true,
			[new Résultat("Incorrecte", "", false, "feedbackNégatif", 100)],
			2,
			1234,
			"Feedback",
		);

		$résultat_attendu = new TentativeProg("python", "testCode", 123456789, true, [], 2, 1234);

		$résultat_attendue = new TentativeProg("python", "testCode", 123456789, true, [], 2, 1234);
		$résultat_observé = (new TentativeDAO())->save("Stefany", "https://exemple.com", $tentative_test);
		$this->assertEquals($résultat_attendu, $résultat_observé);

		$résultat_observé = (new TentativeDAO())->get_tentative("Stefany", "https://exemple.com", 123456789);
		$this->assertEquals($résultat_attendu, $résultat_observé);
	}
}
