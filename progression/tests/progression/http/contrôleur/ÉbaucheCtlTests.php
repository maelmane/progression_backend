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

require_once __DIR__ . '/../../../TestCase.php';

use progression\domaine\entité\{Question, QuestionProg, Exécutable};
use progression\http\contrôleur\ÉbaucheCtl;
use Illuminate\Http\Request;

final class ÉbaucheCtlTests extends TestCase
{
	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_le_chemin_dune_ébauche_lorsquon_appelle_get_on_obtient_lébauche_et_ses_relations_sous_forme_json()
	{
		$_ENV["APP_URL"] = "https://example.com/";

		// Question
		$question = new QuestionProg();
		$question->type = Question::TYPE_PROG;
		$question->chemin = "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction";

		// Ébauches
		$question->exécutables["python"] = new Exécutable("print(\"Hello world\")", "python");
		$question->exécutables["java"] = new Exécutable("System.out.println(\"Hello world\")", "java");

		$résultat_attendu = [
			"data" => [
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
				"/ebauche/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/python",
			);
		$mockRequest
			->allows()
			->query("include")
			->andReturn();
		$this->app->bind(Request::class, function () use ($mockRequest) {
			return $mockRequest;
		});

		// Contrôleur
		$ctl = new ÉbaucheCtl($mockIntFactory);
		$résultat_obtenu = $ctl->get(
			$mockRequest,
			"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
			"python",
		);

		$this->assertEquals(200, $résultat_obtenu->status());
		$this->assertEquals($résultat_attendu, json_decode($résultat_obtenu->getContent(), true));
	}

	public function test_étant_donné_le_chemin_dune_ébauche_inexistante_lorsquon_appelle_get_on_obtient_ressource_non_trouvée()
	{
		$_ENV["APP_URL"] = "https://example.com/";

		// Question
		$question = new QuestionProg();
		$question->type = Question::TYPE_PROG;
		$question->chemin = "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction";

		// Ébauches
		$question->exécutables["python"] = new Exécutable("print(\"Hello world\")", "python");
		$question->exécutables["java"] = new Exécutable("System.out.println(\"Hello world\")", "java");

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
				"/ebauche/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/unlangageinexistant",
			);
		$mockRequest
			->allows()
			->query("include")
			->andReturn();
		$this->app->bind(Request::class, function () use ($mockRequest) {
			return $mockRequest;
		});

		// Contrôleur
		$ctl = new ÉbaucheCtl($mockIntFactory);

		$résultat_obtenu = $ctl->get(
			$mockRequest,
			"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
			"unlangageinexistant",
		);

		$this->assertEquals(404, $résultat_obtenu->status());
		$this->assertEquals('{"erreur":"Ressource non trouvée."}', $résultat_obtenu->getContent());
	}
}
