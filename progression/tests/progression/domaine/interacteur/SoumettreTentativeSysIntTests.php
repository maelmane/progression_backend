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

use progression\domaine\entité\{Question, QuestionSys, RésultatSys, TentativeSys, TestSys, User};
use progression\dao\DAOFactory;
use PHPUnit\Framework\TestCase;
use Mockery;
use progression\dao\question\QuestionDAO;

final class SoumettreTentativeSysIntTests extends TestCase
{
	protected static $questionTests;
	protected static $questionReponseCourte;
	protected static $tentativeSoumise;

	public function setUp(): void
	{
		parent::setUp();

		//Mock User
		$mockUserDao = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDao
			->allows()
			->get_user("jdoe")
			->andReturn(new User("jdoe"));

		// Mock exécuteur
		$mockExécuteur = Mockery::mock("progression\\dao\\exécuteur\\Exécuteur");
		$mockExécuteur
			->shouldReceive("exécuter_sys")
			->withArgs(function ($question, $tentative) {
				return $question == self::$questionTests && $tentative == self::$tentativeSoumise;
			})
			->andReturn([
				"temps_exec" => 0.5,
				"résultats" => [["output" => "Incorrecte", "time" => 0.1]],
			]);

		// Mock DAOFactory
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_exécuteur")->andReturn($mockExécuteur);
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDao);
		DAOFactory::setInstance($mockDAOFactory);

		//Mock Question
		self::$questionTests = new QuestionSys();
		self::$questionTests->titre = "Bonsoir";
		self::$questionTests->niveau = "facile";
		self::$questionTests->uri = "https://example.com/question";
		self::$questionTests->tests = [
			new TestSys(
				nom: "nomTest",
				sortie_attendue: "sortieTest",
				validation: "validationTest",
				utilisateur: "utilisateurTest",
				feedback_pos: "feedbackPositif",
				feedback_neg: "feedbackNégatif",
			),
		];
		self::$questionTests->feedback_neg = "feedbackGénéralNégatif";
		self::$questionTests->feedback_pos = "feedbackGénéralPositif";

		self::$questionReponseCourte = new QuestionSys();
		self::$questionReponseCourte->titre = "Bonsoir";
		self::$questionReponseCourte->niveau = "facile";
		self::$questionReponseCourte->uri = "https://example.com/question";
		self::$questionReponseCourte->solution = "~reponse de test~";
		self::$questionReponseCourte->feedback_neg = "feedbackGénéralNégatif";
		self::$questionReponseCourte->feedback_pos = "feedbackGénéralPositif";

		self::$tentativeSoumise = new TentativeSys("Conteneur de test", "~reponse de test~", 1615696286);
	}

	public function tearDown(): void
	{
		Mockery::close();
		DAOFactory::setInstance(null);
	}

	public function test_étant_donné_une_questionsys_et_une_tentativesys_lorsqu_on_appelle_soumettre_tentative_avec_des_tests_on_obtient_un_objet_tentative_comportant_les_tests_réussis_et_les_résultats()
	{
		$tentative_attendue = new TentativeSys(
			conteneur: "Conteneur de test",
			réponse: "~reponse de test~",
			date_soumission: 1615696286,
			réussi: false,
			tests_réussis: 0,
			temps_exécution: 500,
			feedback: "feedbackGénéralNégatif",
			résultats: [new RésultatSys("Incorrecte", false, "feedbackNégatif", 100)],
		);

		$interacteur = new SoumettreTentativeSysInt();
		$tentative_obtenue = $interacteur->soumettre_tentative("jdoe", self::$questionTests, self::$tentativeSoumise);

		$this->assertEquals($tentative_attendue, $tentative_obtenue);
	}

	public function test_étant_donné_une_questionsys_et_une_tentativesys_lorsqu_on_appelle_soumettre_tentative_avec_une_réponse_courte_on_obtient_un_objet_tentative_comportant_les_tests_réussis_et_les_résultats()
	{
		$tentative_attendue = new TentativeSys(
			conteneur: "Conteneur de test",
			réponse: "~reponse de test~",
			date_soumission: 1615696286,
			réussi: true,
			tests_réussis: 1,
			temps_exécution: 0,
			feedback: "feedbackGénéralPositif",
		);

		$interacteur = new SoumettreTentativeSysInt();
		$tentative_obtenue = $interacteur->soumettre_tentative(
			"jdoe",
			self::$questionReponseCourte,
			self::$tentativeSoumise,
		);

		$this->assertEquals($tentative_attendue, $tentative_obtenue);
	}
}
