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

use progression\dao\DAOFactory;
use progression\domaine\entité\{Test, Exécutable, Question, TentativeProg, QuestionProg, RésultatProg};
use progression\domaine\interacteur\ExécutionException;
use progression\http\contrôleur\TentativeCtl;
use Illuminate\Http\Request;

final class TentativeCtlTests extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();
                
		$_ENV["APP_URL"] = "https://example.com/";

		// Tentative
		$tentative = new TentativeProg("python", "codeTest", "1614374490");
		$tentative->tests_réussis = 2;
		$tentative->feedback = "feedbackTest";

        $mockTentativeDAO = Mockery::mock("progression\dao\TentativeDAO");
		$mockTentativeDAO
            ->shouldReceive("get_tentative")
            ->with("jdoe", "prog1/les_fonctions_01/appeler_une_fonction_paramétrée", "9999999999")
            ->andReturn(null);
		$mockTentativeDAO
            ->shouldReceive("get_tentative")
            ->with("jdoe", "prog1/les_fonctions_01/appeler_une_fonction_paramétrée", "1614374490")
            ->andReturn($tentative);

		// Question
		$question = new QuestionProg();
		$question->type = Question::TYPE_PROG;
		$question->nom = "appeler_une_fonction_paramétrée";
		$question->uri = "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction";
        
		// Ébauches
		$question->exécutables["python"] = new Exécutable("print(\"Hello world\")", "python");
		$question->exécutables["java"] = new Exécutable("System.out.println(\"Hello world\")", "java");
		// Tests
		$question->tests = [new Test("2 salutations", "2", "Bonjour\nBonjour\n")];

        $mockQuestionDAO = Mockery::mock("progression\dao\QuestionDAO");
		$mockQuestionDAO
            ->shouldReceive("get_question")
            ->with("https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
            ->andReturn($question);

        $mockExécuteur = Mockery::mock("progression\dao\Exécuteur");
        $mockExécuteur
            ->shouldReceive("exécuter")
            ->with("python", "codeTest")
            ->andReturn("{\"output\": \"OK\", \"errors\":\"\" }");

		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
		$mockDAOFactory
			->shouldReceive("get_tentative_dao")
			->andReturn($mockTentativeDAO);
		$mockDAOFactory
			->shouldReceive("get_question_dao")
			->andReturn($mockQuestionDAO);
		$mockDAOFactory
			->shouldReceive("get_exécuteur")
			->andReturn($mockExécuteur);
		DAOFactory::setInstance($mockDAOFactory);

    }
    
	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_le_username_dun_utilisateur_le_chemin_dune_question_et_le_timestamp_lorsquon_appelle_get_on_obtient_la_TentativeProg_et_ses_relations_sous_forme_json()
	{
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
				"/tentative/jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU/1614374490",
			);
		$mockRequest
			->allows()
			->query("include")
			->andReturn("resultats");
		$this->app->bind(Request::class, function () use ($mockRequest) {
			return $mockRequest;
		});

		// Contrôleur
		$ctl = new TentativeCtl();
		$résultat_obtenu = $ctl->get(
			$mockRequest,
			"jdoe",
			"cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU",
			"1614374490",
		);

        $résultat_attendu = [
			"data" => [
				"type" => "tentative",
				"id" => "jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU/1614374490",
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


		$this->assertEquals(200, $résultat_obtenu->status());
		$this->assertEquals($résultat_attendu, json_decode($résultat_obtenu->getContent(), true));
	}

	public function test_étant_donné_le_username_dun_utilisateur_le_chemin_dune_question_et_le_timestamp_lorsquon_appelle_get_on_obtient_ressource_non_trouvée()
	{
		$résultat_attendu = [
			"erreur" => "Ressource non trouvée.",
		];

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
				"/tentative/jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU/9999999999",
			);
		$mockRequest
			->allows()
			->query("include")
			->andReturn("resultats");
		$this->app->bind(Request::class, function () use ($mockRequest) {
			return $mockRequest;
		});

		// Contrôleur
		$ctl = new TentativeCtl();
		$résultat_obtenu = $ctl->get(
			$mockRequest,
			"jdoe",
			"cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU",
			"9999999999",
		);

		$this->assertEquals(404, $résultat_obtenu->status());
		$this->assertEquals($résultat_attendu, json_decode($résultat_obtenu->getContent(), true));
	}

	public function test_étant_donné_le_username_dun_utilisateur_le_chemin_dune_question_et_le_timestamp_lorsquon_appelle_post_on_obtient_la_TentativeProg_avec_ses_résultats_et_ses_relations_sous_forme_json()
	{
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
			->all()
			->andReturn(["langage" => "python", "code" => "codeTest"]);
		$mockRequest->allows()->only(["langage", "code"]);
		$mockRequest
			->allows()
			->path()
			->andReturn(
				"/tentative/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
			);
		$mockRequest
			->allows()
			->query("include")
			->andReturn("resultats");
		$this->app->bind(Request::class, function () use ($mockRequest) {
			return $mockRequest;
		});

		// Contrôleur
		$ctl = new TentativeCtl();
		$résultat_obtenu = $ctl->post(
			$mockRequest,
			"jdoe",
			"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
		);
	
		$résultat_attendu = [
			"data" => [
				"type" => "tentative",
				"id" =>
					"jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614374490",
				"attributes" => [
					"date_soumission" => 1614374490,
					"tests_réussis" => 1,
					"réussi" => true,
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
								'id' =>
									'jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614374490/0',
							],
						],
					],
				],
			],
			"included" => [
				[
					"type" => "resultat",
					"id" =>
						"jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614374490/0",
					"attributes" => [
						"numéro" => 0,
						"sortie_erreur" => "",
						"sortie_observée" => "Bonjour\nBonjour\n",
						"résultat" => true,
						"feedback" => "Bon travail!",
					],
					"links" => [
						'self' =>
							'https://example.com/resultat/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614374490/0',
						"related" =>
							'https://example.com/tentative/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614374490',
					],
				],
			],
		];

        $this->assertEquals(200, $résultat_obtenu->status());
		$this->assertEquals($résultat_attendu, json_decode($résultat_obtenu->getContent(), true));
	}

	public function test_étant_donné_une_soumission_sans_code_lorsquon_appelle_post_on_obtient_une_erreur_de_validation()
	{
		$résultat_attendu = [
			"erreur" => [
				"code" => ["Le champ code est obligatoire."],
			],
		];

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
			->all()
			->andReturn(["langage" => "python"]);
		$mockRequest
			->allows()
			->path()
			->andReturn(
				"/tentative/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
			);
		$this->app->bind(Request::class, function () use ($mockRequest) {
			return $mockRequest;
		});

		// Contrôleur
		$ctl = new TentativeCtl();
		$résultat_obtenu = $ctl->post(
			$mockRequest,
			"jdoe",
			"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
		);

		$this->assertEquals(422, $résultat_obtenu->status());
		$this->assertEquals($résultat_attendu, json_decode($résultat_obtenu->getContent(), true));
	}

	public function test_étant_donné_un_url_de_compilebox_inaccessible_lorsquon_appelle_post_on_obtient_Service_non_disponible()
	{
		$_ENV["APP_URL"] = "https://example.com/";
        
		$résultat_attendu = [
			"erreur" => "Service non disponible.",
		];

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
			->all()
			->andReturn(["langage" => "python", "code" => "codeTest"]);
		$mockRequest
			->allows()
			->path()
			->andReturn(
				"/tentative/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
			);
		$this->app->bind(Request::class, function () use ($mockRequest) {
			return $mockRequest;
		});

        $mockExécuteur = Mockery::mock("progression\dao\Exécuteur");
        $mockExécuteur
            ->shouldReceive("exécuter")
            ->with("python", "codeTest")
            ->andReturn("{\"output\": \"OK\", \"errors\":\"\" }");

        DAOFactory::getInstance()
			->shouldReceive("get_exécuteur")
			->andReturn($mockExécuteur);

		// Contrôleur
		$ctl = new TentativeCtl();
		$résultat_obtenu = $ctl->post(
			$mockRequest,
			"jdoe",
			"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
		);

		$this->assertEquals(503, $résultat_obtenu->status());
		$this->assertEquals($résultat_attendu, json_decode($résultat_obtenu->getContent(), true));
	}

	public function test_étant_donné_une_tentative_invalide_lorsquon_appelle_post_on_obtient_Tentative_intraitable()
	{
		$résultat_attendu = [
			"erreur" => "Tentative intraitable.",
		];

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
			->all()
			->andReturn(["langage" => "python", "code" => "#+TODO\ncodeTest"]);
		$mockRequest
			->allows()
			->path()
			->andReturn(
				"/tentative/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
			);
		$this->app->bind(Request::class, function () use ($mockRequest) {
			return $mockRequest;
		});

		// Contrôleur
		$ctl = new TentativeCtl();
		$résultat_obtenu = $ctl->post(
			$mockRequest,
			"jdoe",
			"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
		);

		$this->assertEquals(422, $résultat_obtenu->status());
		$this->assertEquals($résultat_attendu, json_decode($résultat_obtenu->getContent(), true));
	}
}
