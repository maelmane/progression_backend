<?php
/*
  This file is part of Progression.  Progression is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.  Progression is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.  You should have received a copy of the GNU General Public License
  along with Progression.  If not, see <https://www.gnu.org/licenses/>.
*/
require_once __DIR__ . "/../../../TestCase.php";

use progression\domaine\entité\{Question, QuestionProg, Avancement, TentativeProg};
use progression\http\contrôleur\AvancementCtl;
use Illuminate\Http\Request;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

final class AvancementCtlTests extends TestCase
{
	public function test_étant_donné_le_username_dun_utilisateur_et_le_chemin_dune_question_lorsquon_appelle_get_on_obtient_l_avancement_et_ses_relations_sous_forme_json()
	{
		$_ENV["APP_URL"] = "https://example.com/";

		// Question
		$question = new QuestionProg();
		$question->chemin = "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction";

		// Avancement
		$avancement = new Avancement(
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
			"jdoe",
			[new TentativeProg("python", "codeTest", 1614965817, false, 2, "feedbackTest")],
		);
		$avancement->id =
			"jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24";
		$avancement->etat = 1;
		$avancement->type = Question::TYPE_PROG;

		$résultat_attendu = [
			"data" => [
				"type" => "avancement",
				"id" =>
					"jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
				"attributes" => [
					"username" => "jdoe",
					"état" => 1,
				],
				"links" => [
					"self" =>
						"https://example.com/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
				],
				"relationships" => [
					"tentatives" => [
						"links" => [
							"self" =>
								"https://example.com/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/relationships/tentatives",
							"related" =>
								"https://example.com/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/tentatives",
						],
						"data" => [
							[
								"type" => "tentative",
								"id" =>
									"jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614965817",
							],
						],
					],
				],
			],
			"included" => [
				[
					"type" => "tentative",
					"id" =>
						"jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614965817",
					"attributes" => [
						"date_soumission" => 1614965817,
						"tests_réussis" => 2,
						"réussi" => false,
						"sous-type" => "tentativeProg",
						"feedback" => "feedbackTest",
						"langage" => "python",
						"code" => "codeTest",
					],
					"links" => [
						"self" =>
							"https://example.com/tentative/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614965817",
						"related" =>
							"https://example.com/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
					],
					"relationships" => [
						"resultats" => [
							"links" => [
								"self" =>
									"https://example.com/tentative/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614965817/relationships/resultats",
								"related" =>
									"https://example.com/tentative/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614965817/resultats",
							],
						],
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

		$mockObtenirAvancementInt = Mockery::mock("progression\domaine\interacteur\ObtenirAvancementInt");
		$mockObtenirAvancementInt
			->allows()
			->get_avancement("jdoe", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn($avancement);

		// InteracteurFactory
		$mockIntFactory = Mockery::mock("progression\domaine\interacteur\InteracteurFactory");
		$mockIntFactory
			->allows()
			->getObtenirQuestionInt()
			->andReturn($mockObtenirQuestionInt);

		$mockIntFactory
			->allows()
			->getObtenirAvancementInt()
			->andReturn($mockObtenirAvancementInt);

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
				"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
			);
		$mockRequest
			->allows()
			->query("include")
			->andReturn("tentatives");
		$this->app->bind(Request::class, function () use ($mockRequest) {
			return $mockRequest;
		});

		// Contrôleur
		$ctl = new AvancementCtl($mockIntFactory);
		$this->assertEquals(
			$résultat_attendu,
			json_decode(
				$ctl
					->get(
						$mockRequest,
						"jdoe",
						"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
					)
					->getContent(),
				true,
			),
		);
	}
}
