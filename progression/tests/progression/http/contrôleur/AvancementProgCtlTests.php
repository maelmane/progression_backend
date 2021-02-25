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

use progression\domaine\entité\{AvancementProg, RéponseProg, QuestionProg};
use progression\http\contrôleur\AvancementProgCtl;
use Illuminate\Http\Request;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

final class AvancementProgCtlTests extends TestCase
{
    public function test_étant_donné_le_username_dun_utilisateur_et_le_chemin_dune_question_lorsquon_appelle_get_on_obtient_l_avancement_et_ses_relations_sous_forme_json()
    {
        $_ENV['APP_URL'] = 'https://example.com';

        // Question
        $question = new QuestionProg();
        $question->id = 2;
        $question->chemin =
            "prog1/les_fonctions_01/appeler_une_fonction_paramétrée";

        // Avancement
        $avancement = new AvancementProg(2, 1);
        $avancement->lang = 10;
        $avancement->etat = 1;
        $avancement->réponses = [
            new RéponseProg(10, "codeTest")
        ];
        $avancement->réponses[0]->date_soumission = "dateTest";
        $avancement->réponses[0]->tests_réussis = 2;
        $avancement->réponses[0]->feedback = "feedbackTest";

        $résultat_attendu =
            [
                "data" => [
                    "type" => "avancement",
                    "id" => "1/2",
                    "attributes" => [
                        "user_id" => 1,
                        "état" => 1
                    ],
                    "links" => [
                        "self" => "https://example.com/avancement/1/2"
                    ],
                    "relationships" => [
                        "tentatives" => [
                            "links" => [
                                "self" => "https://example.com/avancement/1/2/relationships/tentatives",
                                "related" => "https://example.com/avancement/1/2/tentatives"
                            ],
                            "data" => [
                                [
                                    "type" => "tentative",
                                    "id" => "dateTest"
                                ]
                            ]
                        ]
                    ]
                ],
                "included" => [
                    [
                        "type" => "tentative",
                        "id" => "dateTest",
                        "attributes" => [
                            "date_soumission" => "dateTest",
                            "tests_réussis" => 2,
                            "feedback" => "feedbackTest",
                            "langage" => 10,
                            "code" => "codeTest"
                        ],
                        "links" => [
                            "self" => "https://example.com/tentative/dateTest"
                        ]
                    ]
                ]
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

        $mockObtenirAvancementProgInt = Mockery::mock(
            'progression\domaine\interacteur\ObtenirAvancementProgInt'
        );
        $mockObtenirAvancementProgInt
            ->allows()
            ->get_avancement(1, 2)
            ->andReturn($avancement);

        // InteracteurFactory
        $mockIntFactory = Mockery::mock(
            'progression\domaine\interacteur\InteracteurFactory'
        );
        $mockIntFactory
            ->allows()
            ->getObtenirQuestionProgInt()
            ->andReturn($mockObtenirQuestionProgInt);

        $mockIntFactory
            ->allows()
            ->getObtenirAvancementProgInt()
            ->andReturn($mockObtenirAvancementProgInt);

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
                "/avancement/1/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU="
            );
        $mockRequest
            ->allows()
            ->query("include")
            ->andReturn("tentatives");
        $this->app->bind(Request::class, function () use ($mockRequest) {
            return $mockRequest;
        });

        // Contrôleur
        $ctl = new AvancementProgCtl($mockIntFactory);
        $this->assertEquals(
            $résultat_attendu,
            json_decode(
                $ctl
                    ->get(
                        $mockRequest,
                        1,
                        "cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU="
                    )->getContent(),
                true
            )
        );
    }
}
