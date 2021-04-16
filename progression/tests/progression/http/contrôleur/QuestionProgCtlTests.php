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

require_once __DIR__ . "/../../../TestCase.php";

use progression\domaine\entité\{Question, QuestionProg, Exécutable, Test};
use progression\http\contrôleur\QuestionCtl;
use Illuminate\Http\Request;

final class QuestionProgCtlTests extends TestCase
{
	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_le_chemin_dune_question_lorsquon_appelle_get_on_obtient_la_question_et_ses_relations_sous_forme_json()
	{
		$_ENV["APP_URL"] = "https://example.com/";

		// Question
		$question = new QuestionProg();
		$question->type = Question::TYPE_PROG;
		$question->nom = "appeler_une_fonction_paramétrée";
		$question->uri = "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction";
		$question->titre = "Appeler une fonction paramétrée";
		$question->description = "Appel d'une fonction existante recevant un paramètre";
		$question->enonce =
			"La fonction `salutations` affiche une salution autant de fois que la valeur reçue en paramètre. Utilisez-la pour faire afficher «Bonjour le monde!» autant de fois que le nombre reçu en entrée.";

		// Ébauches
		$question->exécutables["python"] = new Exécutable("print(\"Hello world\")", "python");
		$question->exécutables["java"] = new Exécutable("System.out.println(\"Hello world\")", "java");

		// Tests
		$question->tests = [
			new Test("2 salutations", "2", "Bonjour\nBonjour\n"),
			new Test("Aucune salutation", "0", ""),
		];

		$résultat_attendu = [
			"data" => [
				"type" => "question",
				"id" =>
					"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
				"attributes" => [
					"sous-type" => "questionProg",
					"titre" => "Appeler une fonction paramétrée",
					"description" => "Appel d'une fonction existante recevant un paramètre",
					"énoncé" =>
						"La fonction `salutations` affiche une salution autant de fois que la valeur reçue en paramètre. Utilisez-la pour faire afficher «Bonjour le monde!» autant de fois que le nombre reçu en entrée.",
				],
				"links" => [
					"self" =>
						"https://example.com/question/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
				],
				"relationships" => [
					"tests" => [
						"links" => [
							"self" =>
								"https://example.com/question/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/relationships/tests",
							"related" =>
								"https://example.com/question/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/tests",
						],
						"data" => [
							[
								"type" => "test",
								"id" =>
									"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/0",
							],
							[
								"type" => "test",
								"id" =>
									"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1",
							],
						],
					],
					"ebauches" => [
						"links" => [
							"self" =>
								"https://example.com/question/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/relationships/ebauches",
							"related" =>
								"https://example.com/question/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/ebauches",
						],
						"data" => [
							[
								"type" => "ebauche",
								"id" =>
									"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/python",
							],
							[
								"type" => "ebauche",
								"id" =>
									"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/java",
							],
						],
					],
				],
			],
			"included" => [
				[
					"type" => "test",
					"id" =>
						"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/0",
					"attributes" => [
						"numéro" => 0,
						"nom" => "2 salutations",
						"entrée" => "2",
						"sortie_attendue" => "Bonjour\nBonjour\n",
					],
					"links" => [
						"self" =>
							"https://example.com/test/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/0",
						"related" =>
							"https://example.com/question/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
					],
				],
				[
					"type" => "test",
					"id" =>
						"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1",
					"attributes" => [
						"numéro" => 1,
						"nom" => "Aucune salutation",
						"entrée" => "0",
						"sortie_attendue" => "",
					],
					"links" => [
						"self" =>
							"https://example.com/test/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1",
						"related" =>
							"https://example.com/question/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
					],
				],
				[
					"type" => "ebauche",
					"id" =>
						"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/python",
					"attributes" => [
						"langage" => "python",
						"code" => "print(\"Hello world\")",
					],
					"links" => [
						"self" =>
							"https://example.com/ebauche/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/python",
						"related" =>
							"https://example.com/question/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
					],
				],
				[
					"type" => "ebauche",
					"id" =>
						"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/java",
					"attributes" => [
						"langage" => "java",
						"code" => "System.out.println(\"Hello world\")",
					],
					"links" => [
						"self" =>
							"https://example.com/ebauche/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/java",
						"related" =>
							"https://example.com/question/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
					],
				],
			],
		];

		// Intéracteur
		$mockObtenirQuestionInt = Mockery::mock("progression\domaine\interacteur\ObtenirQuestionInt");
		$mockObtenirQuestionInt
			->allows()
			->get_question("https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn($question);

		// InteracteurFactory
		$mockIntFactory = Mockery::mock("progression\domaine\interacteur\InteracteurFactory");
		$mockIntFactory
			->allows()
			->getObtenirQuestionInt()
			->andReturn($mockObtenirQuestionInt);

		// Requête
		$mockRequest = Mockery::mock("Illuminate\Http\Request");
		$mockRequest
			->allows()
			->offsetGet("username")
			->andReturn("Bob");
		$mockRequest
			->allows()
			->ip()
			->andReturn("127.0.0.1");
		$mockRequest
			->allows()
			->method()
			->andReturn("GET");
		$mockRequest
			->allows()
			->path()
			->andReturn(
				"/question/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
			);
		$mockRequest
			->allows()
			->query("include")
			->andReturn("tests,ebauches");
		$this->app->bind(Request::class, function () use ($mockRequest) {
			return $mockRequest;
		});

		// Contrôleur
		$ctl = new QuestionCtl($mockIntFactory);
		$résultat_obtenu = $ctl->get(
			$mockRequest,
			"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
		);

		$this->assertEquals(200, $résultat_obtenu->status());
		$this->assertEquals($résultat_attendu, json_decode($résultat_obtenu->getContent(), true));
	}

	public function test_étant_donné_le_chemin_dune_question_inexistante_lorsquon_appelle_get_on_obtient_ressource_non_trouvée()
	{
		$_ENV["APP_URL"] = "https://example.com/";

		// Intéracteur
		$mockObtenirQuestionInt = Mockery::mock("progression\domaine\interacteur\ObtenirQuestionInt");
		$mockObtenirQuestionInt
			->allows()
			->get_question("https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction_inexistante")
			->andReturn(null);

		// InteracteurFactory
		$mockIntFactory = Mockery::mock("progression\domaine\interacteur\InteracteurFactory");
		$mockIntFactory
			->allows()
			->getObtenirQuestionInt()
			->andReturn($mockObtenirQuestionInt);

		// Requête
		$mockRequest = Mockery::mock("Illuminate\Http\Request");
		$mockRequest
			->allows()
			->offsetGet("username")
			->andReturn("Bob");
		$mockRequest
			->allows()
			->ip()
			->andReturn("127.0.0.1");
		$mockRequest
			->allows()
			->method()
			->andReturn("GET");
		$mockRequest
			->allows()
			->path()
			->andReturn(
				"/question/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb25faW5leGlzdGFudGU",
			);
		$mockRequest
			->allows()
			->query("include")
			->andReturn("tests,ebauches");
		$this->app->bind(Request::class, function () use ($mockRequest) {
			return $mockRequest;
		});

		// Contrôleur
		$ctl = new QuestionCtl($mockIntFactory);
		$résultat_obtenu = $ctl->get(
			$mockRequest,
			"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb25faW5leGlzdGFudGU",
		);

		$this->assertEquals(404, $résultat_obtenu->status());
		$this->assertEquals('{"erreur":"Ressource non trouvée."}', $résultat_obtenu->getContent());
	}
}
