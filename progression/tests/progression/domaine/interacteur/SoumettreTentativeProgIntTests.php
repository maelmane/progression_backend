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

use progression\domaine\entité\{Exécutable, Avancement, QuestionProg, RésultatProg, TentativeProg, Test};
use progression\dao\DAOFactory;
use progression\dao\tentative\TentativeProgDAO;
use PHPUnit\Framework\TestCase;
use Mockery;

final class SoumettreTentativeProgIntTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		// Avancement actuel
		$avancement = new Avancement();

		$mockAvancementDAO = Mockery::mock("progression\\dao\\AvancementDAO");
		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with(
				"jdoe",
				"https://progression.pages.dti.crosemont.quebec/progression_contenu_demo/les_fonctions_01/appeler_une_fonction_avec_retour",
			)
			->andReturn($avancement);
		$mockAvancementDAO->shouldReceive("save")->andReturn($avancement);

		// Mock TentativeDAO
		$mockTentativeDAO = Mockery::mock("progression\\dao\\tentative\\TentativeProgDAO");
		$mockTentativeDAO
			->shouldReceive("save")
			->with(
				"jdoe",
				"https://progression.pages.dti.crosemont.quebec/progression_contenu_demo/les_fonctions_01/appeler_une_fonction_avec_retour",
				Mockery::any(),
			)
			->andReturnArg(2);

		// Mock exécuteur
		$mockExécuteur = Mockery::mock("progression\\dao\\exécuteur\\Exécuteur");
		$mockExécuteur->shouldReceive("exécuter")->andReturn([["output" => "Patate poil!", "errors" => "" ]]);

		// Mock DAOFactory
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_avancement_dao")->andReturn($mockAvancementDAO);
		$mockDAOFactory->shouldReceive("get_exécuteur")->andReturn($mockExécuteur);
		$mockDAOFactory->shouldReceive("get_tentative_prog_dao")->andReturn($mockTentativeDAO);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_une_questionprog_et_une_tentativeprog_lorsqu_on_appelle_soumettre_tentative_on_obtient_un_objet_tentative_comportant_les_tests_réussis_et_les_résultats()
	{
		// Question
		$question = new QuestionProg();
		$question->uri =
			"https://progression.pages.dti.crosemont.quebec/progression_contenu_demo/les_fonctions_01/appeler_une_fonction_avec_retour";
		$question->tests = [
			new Test(
				"nomTest",
				"sortieTest",
				"entréeTest",
				"params",
				"feedbackPositif",
				"feedbackNégatif",
				"feedbackErreur",
			),
		];
		$question->exécutables["python"] = new Exécutable(
			"#Commentaire invisible\n#+VISIBLE\n#+TODO\nprint()\n#-TODO\n# Rien à faire ici\n#+TODO\n# À faire\n\n",
			"python",
		);
		$question->feedback_neg = "feedbackGénéralNégatif";

		// Tentative soumise
		$tentative = new TentativeProg(
			"python",
			"#Commentaire invisible\n#+VISIBLE\n#+TODO\nprint(\"je fais mon possible!\")\n#-TODO\n# Rien à faire ici\n#+TODO\n# À faire\n\n",
			1615696286,
			false,
			0,
			"feedbackTentativeTest",
		);

		// Exécution
		$interacteur = new SoumettreTentativeProgInt();
		$résultat_obtenu = $interacteur->soumettre_tentative("jdoe", $question, $tentative);

		// Résultat attendu
		$résultat_attendu = new TentativeProg(
			"python",
			"#Commentaire invisible\n#+VISIBLE\n#+TODO\nprint(\"je fais mon possible!\")\n#-TODO\n# Rien à faire ici\n#+TODO\n# À faire\n\n",
			1615696286,
			false,
			0,
			"feedbackGénéralNégatif",
		);

		$résultat_attendu->résultats = [new RésultatProg("Patate poil!", "", false, "feedbackNégatif")];

		// Assertion
		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}
}
