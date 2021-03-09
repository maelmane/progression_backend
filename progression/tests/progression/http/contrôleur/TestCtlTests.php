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

namespace progression\http\contrôleur;

require_once __DIR__ . '/../../../TestCase.php';

use progression\domaine\entité\{QuestionProg, Question, Test};
use progression\http\contrôleur\TestCtl;
use Illuminate\Http\Request;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

final class TestCtlTests extends \TestCase
{
	public function test_étant_donné_le_chemin_dune_question_et_son_test_numero_0_lorsquon_appelle_get_on_obtient_le_test_numero_0_et_ses_relations_sous_forme_json()
	{
		$_ENV["APP_URL"] = "https://example.com/";

		// Question
		$question = new QuestionProg();
		$question->type = Question::TYPE_PROG;
		$question->chemin =
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction";

		// Tests
		$question->tests = [
			new Test("2 salutations", "2", "Bonjour\nBonjour\n"),
			new Test("Aucune salutation", "0", ""),
		];

		$résultat_attendu = [
			"data" => [
				"type" => "test",
				"id" => "aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/0",
				"attributes" => [
					"numéro" => "0",
					"nom" => "2 salutations",
					"entrée" => "2",
					"sortie_attendue" => "Bonjour\nBonjour\n",

				],
				"links" => [
					"self" => "https://example.com/test/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/0",
					"related" => "https://example.com/question/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
				],
			]
		];

		// Intéracteur
		$mockObtenirQuestionInt = Mockery::mock(
			"progression\domaine\interacteur\ObtenirQuestionInt"
		);
		$mockObtenirQuestionInt
			->allows()
			->get_question(
				"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction"
			)
			->andReturn($question);

		// InteracteurFactory
		$mockIntFactory = Mockery::mock(
			"progression\domaine\interacteur\InteracteurFactory"
		);
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
				"/test/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/0"
			);
		$mockRequest
			->allows()
			->query("include")
			->andReturn();
		$this->app->bind(Request::class, function () use ($mockRequest) {
			return $mockRequest;
		});

		// Contrôleur
		$ctl = new TestCtl($mockIntFactory);

		$this->assertEquals(
			$résultat_attendu,
			json_decode(
				$ctl
					->get(
						$mockRequest,
						"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
						"0"
					)
					->getContent(),
				true
			)
		);
	}
}
