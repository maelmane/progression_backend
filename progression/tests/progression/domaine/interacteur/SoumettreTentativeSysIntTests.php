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

use progression\domaine\entité\{Question, QuestionSys, Résultat, TentativeSys, TestSys, User};
use progression\dao\DAOFactory;
use PHPUnit\Framework\TestCase;
use Mockery;
use progression\dao\question\QuestionDAO;

final class SoumettreTentativeSysIntTests extends TestCase
{
	protected static $question_de_test;
	protected static $question_réponse_courte;
	protected static $question_réponse_courte_avec_regex;
	protected static $tentative_correcte;
	protected static $tentative_incorrecte;
	protected static $tentative_soumise_sans_conteneur;

	public function setUp(): void
	{
		parent::setUp();

		//Mock User
		$mockUserDao = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDao
			->allows()
			->get_user("jdoe")
			->andReturn(new User("jdoe"));

		//Tentatives avec conteneur
		self::$tentative_correcte = new TentativeSys(["id" => "Conteneur de test correct"], null, 1615696286);
		self::$tentative_incorrecte = new TentativeSys(["id" => "Conteneur de test incorrect"], null, 1615696286);

		//Tentative sans conteneur
		self::$tentative_soumise_sans_conteneur = new TentativeSys(["id" => null], "", 1615696287);

		// Mock exécuteur
		$mockExécuteur = Mockery::mock("progression\\dao\\exécuteur\\Exécuteur");
		$mockExécuteur
			->shouldReceive("exécuter_sys")
			->withArgs(function ($question, $tentative) {
				return $question == self::$question_de_test && $tentative == self::$tentative_correcte;
			})
			->andReturn([
				"temps_exécution" => 0.5,
				"résultats" => [["output" => "Correcte", "time" => 0.1]],
				"conteneur" => ["id" => "Conteneur de test correct", "ip" => "172.45.2.2", "port" => 45667],
			]);
		$mockExécuteur
			->shouldReceive("exécuter_sys")
			->withArgs(function ($question, $tentative) {
				return $question == self::$question_de_test && $tentative == self::$tentative_incorrecte;
			})
			->andReturn([
				"temps_exécution" => 0.5,
				"résultats" => [["output" => "Incorrecte", "time" => 0.1]],
				"conteneur" => ["id" => "Conteneur de test incorrect", "ip" => "172.45.2.2", "port" => 45667],
			]);

		$mockExécuteur
			->shouldReceive("exécuter_sys")
			->withArgs(function ($question, $tentative) {
				return $question == self::$question_réponse_courte ||
					$question == self::$question_réponse_courte_avec_regex;
			})
			->andReturn([
				"temps_exécution" => 0.5,
				"résultats" => [["output" => "Incorrecte", "time" => 0.1]],
				"conteneur" => ["id" => "Conteneur de test incorrect", "ip" => "172.45.2.2", "port" => 45667],
			]);

		$mockExécuteur
			->shouldReceive("exécuter_sys")
			->withArgs(function ($question, $tentative) {
				return $question == self::$question_de_test && !$tentative->conteneur["id"];
			})
			->andReturn([
				"temps_exécution" => 0.5,
				"résultats" => [["output" => "Incorrecte", "time" => 0.1]],
				"conteneur" => ["id" => "Nouveau Conteneur", "ip" => "172.45.2.2", "port" => 45667],
			]);

		// Mock DAOFactory
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_exécuteur")->andReturn($mockExécuteur);
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDao);
		DAOFactory::setInstance($mockDAOFactory);

		//Mock Question
		self::$question_de_test = new QuestionSys(
			titre: "Bonsoir",
			niveau: "facile",
			tests: [
				new TestSys(
					nom: "nomTest",
					sortie_attendue: "Correcte",
					validation: "validationTest",
					utilisateur: "utilisateurTest",
					feedback_pos: "feedbackPositif",
					feedback_neg: "feedbackNégatif",
				),
			],
			feedback_neg: "feedbackGénéralNégatif",
			feedback_pos: "feedbackGénéralPositif",
		);

		self::$question_réponse_courte = new QuestionSys(
			titre: "Bonsoir",
			niveau: "facile",
			solution: "Bonne réponse",
			feedback_neg: "feedbackGénéralNégatif",
			feedback_pos: "feedbackGénéralPositif",
		);

		self::$question_réponse_courte_avec_regex = new QuestionSys(
			titre: "Bonsoir",
			niveau: "facile",
			solution: "~bonne.réponse~i",
			feedback_neg: "feedbackGénéralNégatif",
			feedback_pos: "feedbackGénéralPositif",
		);
	}

	public function tearDown(): void
	{
		Mockery::close();
		DAOFactory::setInstance(null);
	}

	public function test_étant_donné_une_questionsys_avec_des_tests_lorsquon_soumet_une_tentative_correcte_on_obtient_une_tentative_réussie_avec_temps_dexécution_et_ses_résultats()
	{
		$tentative_attendue = new TentativeSys(
			conteneur: ["id" => "Conteneur de test correct", "ip" => "172.45.2.2", "port" => 45667],
			date_soumission: 1615696286,
			réussi: true,
			tests_réussis: 1,
			temps_exécution: 500,
			feedback: "feedbackGénéralPositif",
			résultats: [new Résultat("Correcte", null, true, "feedbackPositif", 100)],
		);

		$interacteur = new SoumettreTentativeSysInt();
		$tentative_obtenue = $interacteur->soumettre_tentative(
			self::$question_de_test,
			self::$question_de_test->tests,
			new TentativeSys(["id" => "Conteneur de test correct"], null, 1615696286),
		);

		$this->assertEquals($tentative_attendue, $tentative_obtenue);
	}

	public function test_étant_donné_une_questionsys_avec_des_tests_lorsquon_soumet_une_tentative_on_obtient_une_tentative_non_réussie_avec_temps_dexécution_et_ses_résultats()
	{
		$tentative_attendue = new TentativeSys(
			conteneur: ["id" => "Conteneur de test incorrect", "ip" => "172.45.2.2", "port" => 45667],
			date_soumission: 1615696286,
			réussi: false,
			tests_réussis: 0,
			temps_exécution: 500,
			feedback: "feedbackGénéralNégatif",
			résultats: [new Résultat("Incorrecte", "", false, "feedbackNégatif", 100)],
		);

		$interacteur = new SoumettreTentativeSysInt();
		$tentative_obtenue = $interacteur->soumettre_tentative(
			self::$question_de_test,
			self::$question_de_test->tests,
			new TentativeSys(["id" => "Conteneur de test incorrect"], null, 1615696286),
		);

		$this->assertEquals($tentative_attendue, $tentative_obtenue);
	}

	public function test_étant_donné_une_questionsys_avec_solution_courte_lorsquon_soumet_une_réponse_correcte_on_obtient_une_tentative_réussie()
	{
		$tentative_attendue = new TentativeSys(
			conteneur: ["id" => "Conteneur de test incorrect", "ip" => "172.45.2.2", "port" => 45667],
			réponse: "Bonne réponse",
			date_soumission: 1615696286,
			réussi: true,
			tests_réussis: 1,
			temps_exécution: 0,
			feedback: "feedbackGénéralPositif",
			résultats: [],
		);

		$interacteur = new SoumettreTentativeSysInt();
		$tentative_obtenue = $interacteur->soumettre_tentative(
			self::$question_réponse_courte,
			null,
			new TentativeSys(["id" => "Conteneur de test incorrect"], "Bonne réponse", 1615696286),
		);

		$this->assertEquals($tentative_attendue, $tentative_obtenue);
	}

	public function test_étant_donné_une_questionsys_avec_solution_courte_lorsquon_soumet_une_réponse_incorrecte_on_obtient_une_tentative_non_réussie()
	{
		$tentative_attendue = new TentativeSys(
			conteneur: ["id" => "Conteneur de test incorrect", "ip" => "172.45.2.2", "port" => 45667],

			réponse: "Mauvaise réponse",
			date_soumission: 1615696286,
			réussi: false,
			tests_réussis: 0,
			temps_exécution: 0,
			feedback: "feedbackGénéralNégatif",
			résultats: [],
		);

		$interacteur = new SoumettreTentativeSysInt();
		$tentative_obtenue = $interacteur->soumettre_tentative(
			self::$question_réponse_courte,
			null,
			new TentativeSys(["id" => "Conteneur de test incorrect"], "Mauvaise réponse", 1615696286),
		);

		$this->assertEquals($tentative_attendue, $tentative_obtenue);
	}

	public function test_étant_donné_une_questionsys_avec_une_regex_comme_solution_courte_lorsquon_soumet_une_réponse_correcte_on_obtient_une_tentative_réussie()
	{
		$tentative_attendue = new TentativeSys(
			conteneur: ["id" => "Conteneur de test incorrect", "ip" => "172.45.2.2", "port" => 45667],
			réponse: "Bonne réponse",
			date_soumission: 1615696286,
			réussi: true,
			tests_réussis: 1,
			temps_exécution: 0,
			feedback: "feedbackGénéralPositif",
			résultats: [],
		);

		$interacteur = new SoumettreTentativeSysInt();
		$tentative_obtenue = $interacteur->soumettre_tentative(
			self::$question_réponse_courte_avec_regex,
			null,
			new TentativeSys(["id" => "Conteneur de test incorrect"], "Bonne réponse", 1615696286),
		);

		$this->assertEquals($tentative_attendue, $tentative_obtenue);
	}

	public function test_étant_donné_une_questionsys_et_une_tentativesys_sans_conteneur_lorsqu_on_appelle_soumettre_tentative_avec_des_tests_on_obtient_un_objet_tentative_comportant_les_tests_réussis_et_les_résultats_et_lid_du_conteneur()
	{
		$tentative_attendue = new TentativeSys(
			conteneur: ["id" => "Nouveau Conteneur", "ip" => "172.45.2.2", "port" => 45667],
			réponse: "",
			date_soumission: 1615696287,
			réussi: false,
			tests_réussis: 0,
			temps_exécution: 500,
			feedback: "feedbackGénéralNégatif",
			résultats: [new Résultat("Incorrecte", "", false, "feedbackNégatif", 100)],
		);

		$interacteur = new SoumettreTentativeSysInt();
		$tentative_obtenue = $interacteur->soumettre_tentative(
			self::$question_de_test,
			self::$question_de_test->tests,
			self::$tentative_soumise_sans_conteneur,
		);

		$this->assertEquals($tentative_attendue, $tentative_obtenue);
	}
}
