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

use progression\domaine\entité\{QuestionProg, Exécutable, Test};
use progression\http\contrôleur\QuestionProgCtl;
use Illuminate\Http\Request;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

final class QuestionProgCtlTests extends TestCase
{
    public function test_étant_donné_le_chemin_dune_question_lorsquon_appelle_get_on_obtient_la_question_et_ses_relations_sous_forme_json()
    {
        $_ENV['APP_URL'] = 'https://example.com/';

        // Question
        $question = new QuestionProg();
        $question->nom = "appeler_une_fonction_paramétrée";
        $question->chemin =
            "prog1/les_fonctions_01/appeler_une_fonction_paramétrée";
        $question->titre = "Appeler une fonction paramétrée";
        $question->description =
            "Appel d'une fonction existante recevant un paramètre";
        $question->enonce =
            "La fonction `salutations` affiche une salution autant de fois que la valeur reçue en paramètre. Utilisez-la pour faire afficher «Bonjour le monde!» autant de fois que le nombre reçu en entrée.";

        // Ébauches
        $question->exécutables['python'] = new Exécutable("print(\"Hello world\")", "python");
        $question->exécutables['java'] = new Exécutable("System.out.println(\"Hello world\")", "java");

        // Tests
        $question->tests = [
            new Test("2 salutations", "2", "Bonjour\nBonjour\n"),
            new Test("Aucune salutation", "0", ""),
        ];

        $résultat_attendu = [
            "data" => [
                "type" => "question",
                "id" =>
                "cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=",
                "attributes" => [
                    "titre" => "Appeler une fonction paramétrée",
                    "description" =>
                    "Appel d'une fonction existante recevant un paramètre",
                    "énoncé" =>
                    "La fonction `salutations` affiche une salution autant de fois que la valeur reçue en paramètre. Utilisez-la pour faire afficher «Bonjour le monde!» autant de fois que le nombre reçu en entrée.",
                ],
                "links" => [
                    "self" =>
                    "https://example.com/question/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=",
                    "avancement" =>
                    "https://example.com/avancement/Bob/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=",
                ],
                "relationships" => [
                    "tests" => [
                        "links" => [
                            "self" =>
                            "https://example.com/question/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/relationships/tests",
                            "related" =>
                            "https://example.com/question/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/tests",
                        ],
                        "data" => [
                            [
                                "type" => "Test",
                                "id" =>
                                "cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/0",
                            ],
                            [
                                "type" => "Test",
                                "id" =>
                                "cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/1",
                            ],
                        ],
                    ],
                    "ébauches" => [
                        "links" => [
                            "self" =>
                            "https://example.com/question/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/relationships/ébauches",
                            "related" =>
                            "https://example.com/question/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/ébauches",
                        ],
                        "data" => [
                            [
                                "type" => "Ébauche",
                                "id" =>
                                "cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/python",
                            ],
                            [
                                "type" => "Ébauche",
                                "id" =>
                                "cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/java",
                            ],
                        ],
                    ],
                ],
            ],
            "included" => [
                [
                    "type" => "Test",
                    "id" =>
                    "cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/0",
                    "attributes" => [
                        "numéro" => 0,
                        "nom" => "2 salutations",
                        "entrée" => "2",
                        "sortie_attendue" => "Bonjour\nBonjour\n",
                    ],
                    "links" => [
                        "self" =>
                        "https://example.com/test/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/0",
                        "related" =>
                        "https://example.com/question/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=",
                    ],
                ],
                [
                    "type" => "Test",
                    "id" =>
                    "cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/1",
                    "attributes" => [
                        "numéro" => 1,
                        "nom" => "Aucune salutation",
                        "entrée" => "0",
                        "sortie_attendue" => "",
                    ],
                    "links" => [
                        "self" =>
                        "https://example.com/test/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/1",
                        "related" =>
                        "https://example.com/question/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=",
                    ],
                ],
                [
                    "type" => "Ébauche",
                    "id" =>
                    "cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/python",
                    "attributes" => [
                        "langage" => "python",
                        "code" => "print(\"Hello world\")",
                    ],
                    "links" => [
                        "self" =>
                        "https://example.com/ebauche/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/python",
                        "related" =>
                        "https://example.com/question/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=",
                    ],
                ],
                [
                    "type" => "Ébauche",
                    "id" =>
                    "cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/java",
                    "attributes" => [
                        "langage" => "java",
                        "code" => "System.out.println(\"Hello world\")",
                    ],
                    "links" => [
                        "self" =>
                        "https://example.com/ebauche/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/java",
                        "related" =>
                        "https://example.com/question/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=",
                    ],
                ],
            ],
        ];

        // Intéracteur
        $mockObtenirQuestionProgInt = Mockery::mock(
            'progression\domaine\interacteur\ObtenirQuestionProgInt'
        );
        $mockObtenirQuestionProgInt
            ->allows()
            ->get_question(
                'prog1/les_fonctions_01/appeler_une_fonction_paramétrée'
            )
            ->andReturn($question);

        // InteracteurFactory
        $mockIntFactory = Mockery::mock(
            'progression\domaine\interacteur\InteracteurFactory'
        );
        $mockIntFactory
            ->allows()
            ->getObtenirQuestionProgInt()
            ->andReturn($mockObtenirQuestionProgInt);

        // Requête
        $mockRequest = Mockery::mock('Illuminate\Http\Request');
        $mockRequest
            ->allows()
            ->offsetGet('username')
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
                "/question/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU="
            );
        $mockRequest
            ->allows()
            ->query("include")
            ->andReturn("tests,ébauches");
        $this->app->bind(Request::class, function () use ($mockRequest) {
            return $mockRequest;
        });

        // Contrôleur
        $ctl = new QuestionProgCtl($mockIntFactory);

        $this->assertEquals(
            $résultat_attendu,
            json_decode(
                $ctl
                    ->get(
                        $mockRequest,
                        "cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU="
                    )
                    ->getContent(),
                true
            )
        );
    }
}
