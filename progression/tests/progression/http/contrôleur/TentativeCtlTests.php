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

use progression\domaine\entité\{
	Test,
	Exécutable,
	Question,
	AvancementProg,
	TentativeProg,
	QuestionProg,
	RésultatProg
};
use progression\http\contrôleur\TentativeCtl;
use Illuminate\Http\Request;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

final class TentativeCtlTests extends TestCase
{
    public function tearDown() : void {
        Mockery::close();
    }

	public function test_étant_donné_le_username_dun_utilisateur_le_chemin_dune_question_et_le_timestamp_lorsquon_appelle_get_on_obtient_la_TentativeProg_et_ses_relations_sous_forme_json()
	{
		$_ENV["APP_URL"] = "https://example.com/";

		// Tentative
		$tentative = new TentativeProg("python", "codeTest", "1614374490");
		$tentative->id =
			"jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU/1614374490";
		$tentative->tests_réussis = 2;
		$tentative->feedback = "feedbackTest";

		$résultat_attendu = [
			"data" => [
				"type" => "tentative",
				"id" =>
				"jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU/1614374490",
				"attributes" => [
					"date_soumission" => "1614374490",
					"tests_réussis" => 2,
					"réussi" => false,
					"sous-type" => "tentativeProg",
					"feedback" => "feedbackTest",
					"langage" => "python",
					"code" => "codeTest",
				],
				"links" => [
					"self" =>
					"https://example.com/tentative/jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU/1614374490",
				],
				"relationships" => [
					"resultats" => [
						"links" => [
							"self" =>
							"https://example.com/tentative/jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU/1614374490/relationships/resultats",
							"related" =>
							"https://example.com/tentative/jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU/1614374490/resultats",
						],
						"data" => [],
					],
				],
			],
		];

		// Intéracteur
		$mockObtenirTentativeInt = Mockery::mock(
			"progression\domaine\interacteur\ObtenirTentativeInt"
		);
		$mockObtenirTentativeInt
			->allows()
			->get_tentative(
				"jdoe",
				"prog1/les_fonctions_01/appeler_une_fonction_paramétrée",
				"1614374490"
			)
			->andReturn($tentative);

		// InteracteurFactory
		$mockIntFactory = Mockery::mock(
			"progression\domaine\interacteur\InteracteurFactory"
		);
		$mockIntFactory
			->allows()
			->getObtenirTentativeInt()
			->andReturn($mockObtenirTentativeInt);

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
				"/tentative/jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU/1614374490"
			);
		$mockRequest
			->allows()
			->query("include")
			->andReturn("resultats");
		$this->app->bind(Request::class, function () use ($mockRequest) {
			return $mockRequest;
		});

		// Contrôleur
		$ctl = new TentativeCtl($mockIntFactory);
		$this->assertEquals(
			$résultat_attendu,
			json_decode(
				$ctl
					->get(
						$mockRequest,
						"jdoe",
						"cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU",
						"1614374490"
					)
					->getContent(),
				true
			)
		);
	}

	public function test_étant_donné_le_username_dun_utilisateur_le_chemin_dune_question_et_le_timestamp_lorsquon_appelle_post_on_obtient_la_TentativeProg_avec_ses_résultats_et_ses_relations_sous_forme_json()
	{
		$_ENV["APP_URL"] = "https://example.com/";

		// Tentative
		$tentative = new TentativeProg("python", "codeTest", 1614374490);
		$tentative->tests_réussis = 1;
		$tentative->feedback = "feedbackTest";
		$tentative->résultats = [
			new RésultatProg("itération 0\n", "", true, "Bon travail!"),
		];

		// Question
		$question = new QuestionProg();
		$question->type = Question::TYPE_PROG;
		$question->nom = "appeler_une_fonction_paramétrée";
		$question->uri = "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction";
		// Ébauches
		$question->exécutables["python"] = new Exécutable(
			"print(\"Hello world\")",
			"python"
		);
		$question->exécutables["java"] = new Exécutable(
			"System.out.println(\"Hello world\")",
			"java"
		);
		// Tests
		$question->tests = [
			new Test("2 salutations", "2", "Bonjour\nBonjour\n"),
			new Test("Aucune salutation", "0", ""),
		];

		$résultat_attendu = [
			"data" => [
				"type" => "tentative",
				"id" =>
				"jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614374490",
				"attributes" => [
					"date_soumission" => 1614374490,
					"tests_réussis" => 1,
					"réussi" => false,
					"sous-type" => "tentativeProg",
					"feedback" => "feedbackTest",
					"langage" => "python",
					"code" => "codeTest",
				],
				"links" => [
					"self" =>
					"https://example.com/tentative/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614374490",
				],
				"relationships" => [
					"resultats" => [
						"links" => [
							"self" =>
							"https://example.com/tentative/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614374490/relationships/resultats",
							"related" =>
							"https://example.com/tentative/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614374490/resultats",
						],
						"data" => [
							[
								'type' => 'resultat',
								'id' => '0',
							]
						],
					],
				],
			],
			"included" => [
				[
					"type" => "resultat",
					"id" => "0",
					"attributes" => [
						"résultat" => true,
						"sortie_erreur" => "",
						"sortie_observée" => "itération 0\n",
						"feedback" => "Bon travail!",
					],
					"links" => [
						'self' => 'https://example.com/resultat/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614374490/0',
					],
				]
			],
		];

		// Intéracteur
		$mockSoumettreTentativeProgInt = Mockery::mock(
			"progression\domaine\interacteur\SoumettreTentativeProgInt"
		);
		$mockSoumettreTentativeProgInt
			->allows()
			->soumettre_tentative(
				"jdoe",
				$question,
				Mockery::any(),
			)
			->andReturn($tentative);

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
		$mockIntFactory
			->allows()
			->getSoumettreTentativeProgInt()
			->andReturn($mockSoumettreTentativeProgInt);

		// Requête
		$mockRequest = Mockery::mock("Illuminate\Http\Request");
		$mockRequest
			->allows()
			->ip()
			->andReturn("127.0.0.1");
		$mockRequest
			->allows()
			->method()
			->andReturn("POST");
		$mockRequest
			->allows()
			->only(['langage', 'code'])
			->andReturn(["langage" => "python", "code" => "codeTest"]);
		$mockRequest
			->allows()
			->path()
			->andReturn(
				"/tentative/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24"
			);
		$mockRequest
			->allows()
			->query("include")
			->andReturn("resultats");
		$this->app->bind(Request::class, function () use ($mockRequest) {
			return $mockRequest;
		});

		// Contrôleur
		$ctl = new TentativeCtl($mockIntFactory);
		$this->assertEquals(
			$résultat_attendu,
			json_decode(
				$ctl
					->post(
						$mockRequest,
						"jdoe",
						"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
					)
					->getContent(),
				true
			)
		);
	}
}
