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

use progression\domaine\entité\question\{Question, État};
use progression\domaine\entité\{Avancement, TentativeProg, Sauvegarde};
use progression\TestCase;

final class AvancementDAOTests extends TestCase
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

	public function test_étant_donné_un_avancement_existant_lorsquon_cherche_par_username_et_question_uri_incluant_les_tentatives_on_obtient_un_objet_avancement_correspondant_avec_ses_tentatives()
	{
		$résultat_attendu = new Avancement(
			[
				1615696276 => new TentativeProg("python", 'print("Tourlou le monde!")', 1615696276, false, [], 2, 3456),
			],
			titre: "Un titre",
			niveau: "facile",
		);
		$résultat_attendu->date_modification = 1615696276;

		$résponse_observée = (new AvancementDAO())->get_avancement(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
			includes: ["tentatives"],
		);
		$this->assertEquals($résultat_attendu, $résponse_observée);
	}

	public function test_étant_donné_un_avancement_existant_lorsquon_cherche_par_username_et_question_uri_incluant_les_sauvegardes_on_obtient_un_objet_avancement_correspondant_avec_ses_sauvegardes()
	{
		$résultat_attendu = new Avancement(
			[],
			titre: "Un titre",
			niveau: "facile",
			sauvegardes: [
				"python" => new Sauvegarde(1620150294, "print(\"Hello world!\")"),
				"java" => new Sauvegarde(1620150375, "System.out.println(\"Hello world!\");"),
			],
		);
		$résultat_attendu->date_modification = 1615696276;
		$résultat_attendu->etat = État::NONREUSSI;

		$résponse_observée = (new AvancementDAO())->get_avancement(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
			includes: ["sauvegardes"],
		);
		$this->assertEquals($résultat_attendu, $résponse_observée);
	}

	public function test_étant_donné_un_avancement_existant_lorsquon_cherche_par_username_et_question_uri_incluant_les_tentatives_et_sauvegardes_on_obtient_un_objet_avancement_correspondant_avec_ses_tentatives_et_sauvegardes()
	{
		$résultat_attendu = new Avancement(
			tentatives: [
				1615696276 => new TentativeProg("python", 'print("Tourlou le monde!")', 1615696276, false, [], 2, 3456),
			],
			titre: "Un titre",
			niveau: "facile",
			sauvegardes: [
				"python" => new Sauvegarde(1620150294, "print(\"Hello world!\")"),
				"java" => new Sauvegarde(1620150375, "System.out.println(\"Hello world!\");"),
			],
		);

		$résponse_observée = (new AvancementDAO())->get_avancement(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
			includes: ["tentatives", "sauvegardes"],
		);
		$this->assertEquals($résultat_attendu, $résponse_observée);
	}

	public function test_étant_donné_un_utilisateur_ayant_des_avancements_lorsquon_cherche_par_username_on_obtient_un_tableau_d_objets_avancement()
	{
		$av0 = new Avancement(tentatives: [], titre: "Un titre", niveau: "facile");
		$av0->date_modification = 1615696276;
		$av0->etat = État::NONREUSSI;

		$av1 = new Avancement(tentatives: [], titre: "Un titre", niveau: "facile", sauvegardes: []);
		$av1->date_modification = 1645739981;
		$av1->date_réussite = 1645739959;
		$av1->etat = État::NONREUSSI;

		$av2 = new Avancement(tentatives: [], titre: "Un titre 2", niveau: "facile", sauvegardes: []);
		$av2->date_modification = 1645739991;
		$av2->date_réussite = 1645739969;
		$av2->etat = État::REUSSI;

		$résultat_attendu = [
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction" => $av0,
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction" => $av1,
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction2" => $av2,
		];

		$résponse_observée = (new AvancementDAO())->get_tous("bob");
		$this->assertEquals($résultat_attendu, $résponse_observée);
	}

	public function test_étant_donné_un_utilisateur_sans_avancement_lorsquon_cherche_par_username_on_obtient_un_tableau_vide()
	{
		$résultat_attendu = [];

		$résponse_observée = (new AvancementDAO())->get_tous("admin");
		$this->assertEquals($résultat_attendu, $résponse_observée);
	}

	public function test_étant_donné_un_avancement_inexistant_lorsquon_le_cherche_par_username_et_question_uri_on_obtient_null()
	{
		$résponse_observée = (new AvancementDAO())->get_avancement(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction_inexistante",
		);
		$this->assertNull($résponse_observée);
	}

	public function test_étant_donné_un_nouvel_avancement_lorsquon_le_sauvegarde_il_est_sauvegardé_dans_la_BD_et_on_obtient_lavancement()
	{
		$nouvel_avancement = new Avancement(tentatives: [], titre: "Un titre", niveau: "facile");
		$nouvel_avancement->date_modification = 1645739981;
		$nouvel_avancement->date_réussite = 1645739959;

		// L'avancement est retourné
		$résponse_observée = (new AvancementDAO())->save(
			"bob",
			"https://depot.com/roger/une_nouvelle_question",
			$nouvel_avancement,
		);

		$this->assertEquals($nouvel_avancement, $résponse_observée);

		// L'avancement a été sauvegardé
		$résponse_observée = (new AvancementDAO())->get_avancement(
			"bob",
			"https://depot.com/roger/une_nouvelle_question",
		);

		$this->assertEquals($nouvel_avancement, $résponse_observée);
	}

	public function test_étant_donné_un_avancement_existant_lorsquon_le_modifie_il_est_sauvegardé_dans_la_BD_et_on_obtient_lavancement_modifié()
	{
		$résultat_attendu = new Avancement(
			tentatives: [],
			titre: "Nouveau titre",
			niveau: "Nouveau niveau",
			sauvegardes: [],
		);

		// L'avancement est retourné
		$résponse_observée = (new AvancementDAO())->save(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
			$résultat_attendu,
		);

		$this->assertEquals($résultat_attendu, $résponse_observée);

		// L'avancement a été sauvegardé
		$résponse_observée = (new AvancementDAO())->get_avancement(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
		);

		$this->assertEquals($résultat_attendu, $résponse_observée);
	}
}
