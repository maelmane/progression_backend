<?php
/*
  This file is part of Progression.

  Progression is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.
s
  Progression is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with Progression.  If not, see <https://www.gnu.org/licenses/>.
*/

namespace progression\http\transformer;

use progression\domaine\entité\QuestionProg;
use progression\domaine\entité\Exécutable;
use progression\domaine\entité\Test;
use PHPUnit\Framework\TestCase;

final class QuestionProgTransformerTests extends TestCase
{
    public function test_étant_donné_une_questionprog_instanciée_avec_des_valeurs_lorsquon_le_transforme_on_obtient_un_tableau_d_objets_identique_avec_les_liens_avancement()
    {
        $_ENV["APP_URL"] = "https://example.com/";
        $username = "jdoe";

        $question = new QuestionProg();
        $question->nom = "appeler_une_fonction_paramétrée";
        $question->uri =
            "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction";
        $question->titre = "Appeler une fonction paramétrée";
        $question->description =
            "Appel d\'une fonction existante recevant un paramètre";
        $question->enonce =
            "La fonction `salutations` affiche une salution autant de fois que la valeur reçue en paramètre. Utilisez-la pour faire afficher «Bonjour le monde!» autant de fois que le nombre reçu en entrée.";

        $résultat = [
            "id" =>
                "aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
            "sous-type" => "questionProg",
            "titre" => "Appeler une fonction paramétrée",
            "description" =>
                "Appel d\'une fonction existante recevant un paramètre",
            "énoncé" =>
                "La fonction `salutations` affiche une salution autant de fois que la valeur reçue en paramètre. Utilisez-la pour faire afficher «Bonjour le monde!» autant de fois que le nombre reçu en entrée.",
            "links" => [
                "self" =>
                    "https://example.com/question/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
                "avancement" =>
                    "https://example.com/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
            ],
        ];

        $item = (new QuestionProgTransformer())->transform([
            "question" => $question,
            "username" => $username,
        ]);
        $this->assertEquals($résultat, $item);
    }

    public function test_étant_donné_une_question_avec_ses_tests_lorsquon_inclut_les_tests_on_reçoit_un_tableau_de_tests_numérotés_dans_le_même_ordre()
    {
        $_ENV["APP_URL"] = "https://example.com/";

        $question = new QuestionProg();
        $question->uri =
            "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction";

        $question->tests = [
            new Test("2 salutations", "2", "Bonjour\nBonjour\n"),
            new Test("Aucune salutation", "0", ""),
        ];

        $résultats_attendus = [
            [
                "id" =>
                    "aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/0",
                "numéro" => 0,
                "nom" => "2 salutations",
                "entrée" => "2",
                "sortie_attendue" => "Bonjour\nBonjour\n",
                "links" => [
                    "self" =>
                        "https://example.com/test/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/0",
                    "related" =>
                        "https://example.com/question/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
                ],
            ],
            [
                "id" =>
                    "aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1",
                "numéro" => 1,
                "nom" => "Aucune salutation",
                "entrée" => "0",
                "sortie_attendue" => "",
                "links" => [
                    "self" =>
                        "https://example.com/test/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1",
                    "related" =>
                        "https://example.com/question/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
                ],
            ],
        ];

        $questionProgTransformer = new QuestionProgTransformer();

        $résultats_obtenus = $questionProgTransformer->includeTests([
            "question" => $question,
            "username" => "Bob",
        ]);

        foreach ($résultats_obtenus->getData() as $i => $résultat_obtenu) {
            $this->assertEquals(
                $résultats_attendus[$i],
                $résultats_obtenus
                    ->getTransformer()
                    ->transform($résultat_obtenu)
            );
        }
    }

    public function test_étant_donné_une_question_sans_tests_lorsquon_inclut_les_tests_on_reçoit_un_tableau_vide()
    {
        $question = new QuestionProg();

        $question->tests = [];

        $questionProgTransformer = new QuestionProgTransformer();
        $résultat_obtenu = $questionProgTransformer->includeTests([
            "question" => $question,
            "username" => "Bob",
        ]);

        $this->assertEquals(0, count($résultat_obtenu->getData()));
    }

    public function test_étant_donné_une_question_avec_ses_ébauches_lorsquon_inclut_les_ébauches_on_reçoit_un_tableau_débauches()
    {
        $question = new QuestionProg();
        $question->uri =
            "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction";

        $question->exécutables = [
            new Exécutable("print(\"Hello world\")", "python"),
            new Exécutable("System.out.println(\"Hello world\")", "java"),
        ];

        $résultats_attendus = [
            [
                "id" =>
                    "aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/python",
                "langage" => "python",
                "code" => "print(\"Hello world\")",
                "links" => [
                    "self" =>
                        "https://example.com/ebauche/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/python",
                    "related" =>
                        "https://example.com/question/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
                ],
            ],
            [
                "id" =>
                    "aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/java",
                "langage" => "java",
                "code" => "System.out.println(\"Hello world\")",
                "links" => [
                    "self" =>
                        "https://example.com/ebauche/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/java",
                    "related" =>
                        "https://example.com/question/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
                ],
            ],
        ];

        $questionProgTransformer = new QuestionProgTransformer();

        $résultats_obtenus = $questionProgTransformer->includeEbauches([
            "question" => $question,
            "username" => "Bob",
        ]);

        foreach ($résultats_obtenus->getData() as $i => $résultat_obtenu) {
            $this->assertEquals(
                $résultats_attendus[$i],
                $résultats_obtenus
                    ->getTransformer()
                    ->transform($résultat_obtenu)
            );
        }
    }

    public function test_étant_donné_une_question_sans_ébauche_lorsquon_inclut_les_ébauches_on_reçoit_un_tableau_vide()
    {
        $question = new QuestionProg();

        $question->exécutables = [];

        $questionProgTransformer = new QuestionProgTransformer();
        $résultat_obtenu = $questionProgTransformer->includeEbauches([
            "question" => $question,
            "username" => "Bob",
        ]);

        $this->assertEquals(0, count($résultat_obtenu->getData()));
    }
}
