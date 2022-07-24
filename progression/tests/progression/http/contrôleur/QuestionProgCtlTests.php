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

use progression\ContrôleurTestCase;

use progression\domaine\entité\{Question, QuestionProg, Exécutable, Test, User};
use progression\dao\DAOFactory;
use progression\dao\question\ChargeurException;
use Illuminate\Auth\GenericUser;

final class QuestionProgCtlTests extends ContrôleurTestCase
{
	public $user;

	public function setUp(): void
	{
		parent::setUp();

		$_ENV["APP_URL"] = "https://example.com/";

		$this->user = new GenericUser(["username" => "bob", "rôle" => User::ROLE_NORMAL]);

		// Question
		$question = new QuestionProg();
		$question->type = Question::TYPE_PROG;
		$question->uri = "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction";
		$question->titre = "Appeler une fonction paramétrée";
		$question->description = "Appel d'une fonction existante recevant un paramètre";
		$question->enonce =
			"La fonction `salutations` affiche une salution autant de fois que la valeur reçue en paramètre. Utilisez-la pour faire afficher «Bonjour le monde!» autant de fois que le nombre reçu en entrée.";
		$question->auteur = "Albert Einstein";
		$question->licence = "poétique";
		$question->niveau = "débutant";
		// Ébauches
		$question->exécutables["python"] = new Exécutable("print(\"Hello world\")", "python");
		$question->exécutables["java"] = new Exécutable("System.out.println(\"Hello world\")", "java");

		// Tests
		$question->tests = [
			new Test("2 salutations", "Bonjour\nBonjour\n", "2"),
			new Test("Aucune salutation", "", "0"),
		];

		$mockQuestionDAO = Mockery::mock("progression\\dao\\question\\QuestionDAO");
		$mockQuestionDAO
			->shouldReceive("get_question")
			->with("https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn($question);
		$mockQuestionDAO
			->shouldReceive("get_question")
			->with(Mockery::any())
			->andThrow(new ChargeurException());

		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_question_dao")->andReturn($mockQuestionDAO);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
		DAOFactory::setInstance(null);
	}

	public function test_étant_donné_le_chemin_dune_question_lorsquon_appelle_get_on_obtient_la_question_et_ses_relations_sous_forme_json()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"GET",
			"/question/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
		);

		$this->assertEquals(200, $résultat_obtenu->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/questionCtlTests_question_prog.json",
			$résultat_obtenu->getContent(),
		);
	}

	public function test_étant_donné_le_chemin_dune_question_inexistante_lorsquon_appelle_get_on_obtient_une_erreur_400()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"GET",
			"/question/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmV",
		);

		$this->assertEquals(400, $résultat_obtenu->status());
		$this->assertEquals('{"erreur":"Question indisponible"}', $résultat_obtenu->getContent());
	}
}
