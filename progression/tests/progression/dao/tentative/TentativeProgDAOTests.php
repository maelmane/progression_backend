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

use progression\domaine\entité\{TentativeProg, Résultat, Commentaire, User};
use progression\TestCase;
use progression\dao\{DAOException, DAOFactory};
use progression\dao\EntitéDAO;

final class TentativeProgDAOTests extends TestCase
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

	public function test_étant_donné_une_TentativeProg_non_réussie_lorsquon_récupère_la_tentative_sans_inclusion_on_obtient_une_tentative_de_type_prog()
	{
		$résultat_attendu = new TentativeProg("python", "print(\"Tourlou le monde!\")", 1615696276, false, [], 2, 3456);
		$résultat_observé = (new TentativeDAO())->get_tentative(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
			1615696276,
		);

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}

	public function test_étant_donné_une_TentativeProg_non_réussie_lorsquon_récupère_la_tentative_en_incluant_les_commentaires_et_leur_créateur_on_obtient_une_tentative_de_type_prog_avec_ses_commentaires_et_leur_créateur()
	{
		$this->jdoe = new User("jdoe");
		$this->admin = new User("admin", rôle: User::RÔLE::ADMIN);
		$this->stefany = new User("Stefany");

		$commentaires = [];
		$commentaires[1] = new Commentaire("le 1er message", $this->jdoe, 1615696277, 14);
		$commentaires[2] = new Commentaire("le 2er message", $this->admin, 1615696278, 12);
		$commentaires[3] = new Commentaire("le 3er message", $this->stefany, 1615696279, 14);

		$résultat_attendu = new TentativeProg(
			"python",
			"print(\"Tourlou le monde!\")",
			1615696276,
			false,
			[],
			2,
			3456,
			null,
			$commentaires,
		);
		$résultat_observé = (new TentativeDAO())->get_tentative(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
			1615696276,
			["commentaires", "commentaires.créateur"],
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
		$résultat_observé = (new TentativeDAO())->save("stefany", "https://exemple.com", $tentative_test);
		$this->assertEquals($résultat_attendu, $résultat_observé);

		$résultat_observé = (new TentativeDAO())->get_tentative("stefany", "https://exemple.com", 123456789);
		$this->assertEquals($résultat_attendu, $résultat_observé);
	}
}
