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

use progression\domaine\entité\{Exécutable, Avancement, Question, QuestionProg, RésultatProg, TentativeProg, Test, User};
use progression\dao\DAOFactory;
use progression\dao\tentative\TentativeProgDAO;
use PHPUnit\Framework\TestCase;
use Mockery;
use progression\dao\question\QuestionDAO;

final class SoumettreTentativeProgIntTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();
		/*
		$mockQuestionDao = Mockery::mock("progression\\dao\\question\\QuestionDAO");
		$mockQuestionDao
			->shouldReceive("get_question")
			->with("file:///prog1/les_fonctions/appeler_une_fonction/info.yml")
			->andReturn($question);
		*/

		//Mock User
		$mockUserDao = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDao
			->allows()
			->get_user("jdoe")
			->andReturn(new User("jdoe"));
		


		//Mock Question
		$question = new QuestionProg();
		$question->titre = "Question de test";
		$question->exécutables = ["python" => new Exécutable("print(\"Allo le monde\")", "python")];
		$question->tests = [0 => new Test("#1", "Allo le monde", "")];
		$question->uri = "https://progression.pages.dti.crosemont.quebec/progression_contenu_demo/les_fonctions_01/appeler_une_fonction_avec_retour";
		
		$mockQuestionDAO = Mockery::mock("progression\\dao\\question\\QuestionDAO");
		$mockQuestionDAO 
			->shouldReceive("get_question")
			->with(
				"https://progression.pages.dti.crosemont.quebec/progression_contenu_demo/les_fonctions_01/appeler_une_fonction_avec_retour"

			)
			->andReturn($question);
		

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
		$mockDAOFactory->shouldReceive("get_question_dao")->andReturn($mockQuestionDAO);
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDao);
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
	/*
	//récupérerAvancement()
	public function test_récupérerAvancement_null_retourne_un_avancement(){
		$question = $this->bidon_question();
		$tentative = $this->bidon_tentative();



		//Mocker get_avancement
		DAOFactory::getInstance()
		->créerAvancement()
		->shouldReceive("créerAvancement")
		->once()
		->withArgs(

			function($tentative, $question) use($tentative, $question){
				return $tentative->
			}

		)
		->andReturn($avancement);
		
		//Mocker créerAvancement
	}
	*/
	//créerAvancement

	//mettreÀJourDateModificationEtDateRéussiePourAvancement

	//sauvegarderAvancement


	//récupérer_informations_de_la_question



	private function bidon_question(){
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

		return $question;
	}

	private function bidon_tentative(){
		$tentative = new TentativeProg(
			"python",
			"#Commentaire invisible\n#+VISIBLE\n#+TODO\nprint(\"je fais mon possible!\")\n#-TODO\n# Rien à faire ici\n#+TODO\n# À faire\n\n",
			1615696286,
			false,
			0,
			"feedbackTentativeTest",
		);
	}
}
