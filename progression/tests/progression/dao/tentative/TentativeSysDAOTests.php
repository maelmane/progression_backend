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

use progression\domaine\entité\{TentativeSys, Résultat};
use PHPUnit\Framework\TestCase;
use progression\dao\{DAOException, DAOFactory};
use progression\dao\EntitéDAO;

final class TentativeSysDAOTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();
		app("db")
			->connection()
			->beginTransaction();
	}

	public function tearDown(): void
	{
		app("db")
			->connection()
			->rollBack();
		parent::tearDown();
	}

	public function test_étant_donné_une_TentativeSys_non_réussie_lorsquon_récupère_la_tentative_on_obtient_une_tentative_de_type_sys()
	{
		$résultat_attendu = new TentativeSys(["id" => "leConteneur"], "laRéponse", 1615696300, false, [], 0, 0);
		$résultat_observé = (new TentativeDAO())->get_tentative(
			"jdoe",
			"https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions",
			1615696300,
		);

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}

	public function test_étant_donné_une_TentativeSys_réussie_lorsquon_récupère_la_tentative_on_obtient_une_tentative_de_type_sys()
	{
		$résultat_attendu = new TentativeSys(["id" => "leConteneur2"], "laRéponse2", 1615696301, true, [], 1, 0);

		$résultat_observé = (new TentativeDAO())->get_tentative(
			"jdoe",
			"https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions",
			1615696301,
		);

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}

	public function test_étant_donné_une_TentativeSys_lorsquon_récupère_toutes_les_tentatives_on_obtient_un_tableau_de_tentatives()
	{
		$résultat_attendue = [
			new TentativeSys(["id" => "leConteneur"], "laRéponse", 1615696300, false, [], 0, 0),
			new TentativeSys(["id" => "leConteneur2"], "laRéponse2", 1615696301, true, [], 1, 0),
		];

		$résultat_observé = (new TentativeDAO())->get_toutes(
			"jdoe",
			"https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions",
		);

		$this->assertEquals($résultat_attendue, $résultat_observé);
	}

	public function test_étant_donné_une_TentativeSys_lorsquon_sauvegarde_la_tentative_on_obtient_une_nouvelle_insertion_dans_la_table_reponse_prog()
	{
		$tentative_test = new TentativeSys(
			["id" => "leConteneur"],
			"laRéponse3",
			1615696400,
			false,
			[new Résultat("Incorrecte", "", false, "feedbackNégatif", 0)],
			1,
			2,
			"feedbackNégatif",
		);

		$résultat_attendu = new TentativeSys(["id" => "leConteneur"], "laRéponse3", 1615696400, false, [], 1, 2);

		$résultat_observé = (new TentativeDAO())->save("jdoe", "https://exemple2.com", $tentative_test);

		$this->assertEquals($résultat_attendu, $résultat_observé);

		$résultat_observé = (new TentativeDAO())->get_tentative("jdoe", "https://exemple2.com", 1615696400);
		$this->assertEquals($résultat_attendu, $résultat_observé);
	}
}
