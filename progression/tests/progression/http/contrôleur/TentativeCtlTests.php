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
require_once __DIR__ . '/../../../TestCase.php';

use progression\domaine\entité\{Question, AvancementProg, TentativeProg, QuestionProg};
use progression\http\contrôleur\TentativeCtl;
use Illuminate\Http\Request;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

final class TentativeCtlTests extends TestCase
{
	public function test_étant_donné_le_username_dun_utilisateur_le_chemin_dune_question_et_le_timestamp_lorsquon_appelle_get_on_obtient_la_tentative_et_ses_relations_sous_forme_json()
	{
		$_ENV['APP_URL'] = 'https://example.com/';

		// Tentative
		$tentative = new TentativeProg("python", "codeTest", "1614374490");
		$tentative->id = "jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU/1614374490";
		$tentative->tests_réussis = 2;
		$tentative->feedback = "feedbackTest";

		$résultat_attendu =
			[
				"data" => [
					"type" => "tentative",
					"id" => "jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU/1614374490",
					"attributes" => [
						"date_soumission" => "1614374490",
						"tests_réussis" => 2,
						"feedback" => "feedbackTest",
						"langage" => "python",
						"code" => "codeTest"
					],
					"links" => [
						"self" => "https://example.com/tentative/jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU/1614374490"
					],
					"relationships" => [
						"resultats" => [
							"links" => [
								"self" => "https://example.com/tentative/jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU/1614374490/relationships/resultats",
								"related" => "https://example.com/tentative/jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU/1614374490/resultats"
							],
							"data" => []
						]
					]
				]
			];


		// Intéracteur
		$mockObtenirTentativeInt = Mockery::mock(
			'progression\domaine\interacteur\ObtenirTentativeInt'
		);
		$mockObtenirTentativeInt
			->allows()
			->get_tentative("jdoe", "prog1/les_fonctions_01/appeler_une_fonction_paramétrée", "1614374490")
			->andReturn($tentative);

		// InteracteurFactory
		$mockIntFactory = Mockery::mock(
			'progression\domaine\interacteur\InteracteurFactory'
		);
		$mockIntFactory
			->allows()
			->getObtenirTentativeInt()
			->andReturn($mockObtenirTentativeInt);

		// Requête
		$mockRequest = Mockery::mock('Illuminate\Http\Request');
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
					)->getContent(),
				true
			)
		);
	}
}
