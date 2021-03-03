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

use progression\domaine\entité\{Question, QuestionProg, Exécutable, Test};
use progression\http\contrôleur\ÉbaucheCtl;
use Illuminate\Http\Request;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

final class ÉbaucheCtlTests extends TestCase
{
    public function test_étant_donné_le_chemin_dune_ébauche_lorsquon_appelle_get_on_obtient_lébauche_et_ses_relations_sous_forme_json()
    {
        $_ENV["APP_URL"] = "https://example.com/";

        // Question
        $question = new QuestionProg();
        $question->type = Question::TYPE_PROG;
        $question->nom = "appeler_une_fonction_paramétrée";
        $question->chemin =
            "https://uri.com/prog1/les_fonctions_01/appeler_une_fonction_paramétrée";
        $question->titre = "Appeler une fonction paramétrée";
        $question->description =
            "Appel d'une fonction existante recevant un paramètre";
        $question->enonce =
            "La fonction `salutations` affiche une salution autant de fois que la valeur reçue en paramètre. Utilisez-la pour faire afficher «Bonjour le monde!» autant de fois que le nombre reçu en entrée.";

        // Ébauches
        $question->exécutables["python"] = new Exécutable("print(\"Hello world\")", "python");
        $question->exécutables["java"] = new Exécutable("System.out.println(\"Hello world\")", "java");

        $résultat_attendu = [
            "data" => [
                "type" => "ebauche",
                "id" => "aHR0cHM6Ly91cmkuY29tL3Byb2cxL2xlc19mb25jdGlvbnNfMDEvYXBwZWxlcl91bmVfZm9uY3Rpb25fcGFyYW3DqXRyw6ll/python",
                "attributes" => [
                    "langage" => "python",
                    "code" => "print(\"Hello world\")",
                ],
                "links" => [
                    "self" => "https://example.com/ebauche/aHR0cHM6Ly91cmkuY29tL3Byb2cxL2xlc19mb25jdGlvbnNfMDEvYXBwZWxlcl91bmVfZm9uY3Rpb25fcGFyYW3DqXRyw6ll/python",
                    "related" => "https://example.com/question/aHR0cHM6Ly91cmkuY29tL3Byb2cxL2xlc19mb25jdGlvbnNfMDEvYXBwZWxlcl91bmVfZm9uY3Rpb25fcGFyYW3DqXRyw6ll",
                ],
            ],
        ];

        // Intéracteur
        $mockObtenirQuestionInt = Mockery::mock(
            "progression\domaine\interacteur\ObtenirQuestionInt"
        );
        $mockObtenirQuestionInt
            ->allows()
            ->get_question(
                "https://uri.com/prog1/les_fonctions_01/appeler_une_fonction_paramétrée"
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
                "/ebauche/aHR0cHM6Ly91cmkuY29tL3Byb2cxL2xlc19mb25jdGlvbnNfMDEvYXBwZWxlcl91bmVfZm9uY3Rpb25fcGFyYW3DqXRyw6ll/python"
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

        $this->assertEquals(
            $résultat_attendu,
            json_decode(
                $ctl
                    ->get(
                        $mockRequest,
                        "aHR0cHM6Ly91cmkuY29tL3Byb2cxL2xlc19mb25jdGlvbnNfMDEvYXBwZWxlcl91bmVfZm9uY3Rpb25fcGFyYW3DqXRyw6ll",
                        "python"
                    )
                    ->getContent(),
                true
            )
        );
    }
}
