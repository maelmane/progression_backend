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

namespace progression\http\transformer;

use progression\domaine\entité\{AvancementProg, TentativeProg};
use PHPUnit\Framework\TestCase;

final class AvancementProgTransformerTests extends TestCase
{
    public function test_étant_donné_un_avancement_avec_ses_tentatives_lorsquon_inclut_les_tentatives_on_reçoit_un_tableau_de_tentatives()
    {
        $_ENV["APP_URL"] = "https://example.com/";

        $avancement = new AvancementProg(
            "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
            "jdoe"
        );
        $avancement->id =
            "jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24";
        $avancement->tentatives = [];
        $avancement->tentatives[] = new TentativeProg(
            "python",
            "codeTestPython",
            1614711760,
            2,
            false,
            "feedbackTest Python"
        );
        $avancement->tentatives[] = new TentativeProg(
            "java",
            "codeTestJava",
            1614711761,
            2,
            true,
            "feedbackTest Java"
        );

        $résultats_attendus = [
            [
                "id" =>
                    "jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614711760",
                "date_soumission" => 1614711760,
                "tests_réussis" => 2,
                "feedback" => "feedbackTest Python",
                "langage" => "python",
                "code" => "codeTestPython",
                "réussi" => false,
                "sous-type" => "tentativeProg",
                "links" => [
                    "self" =>
                        "https://example.com/tentative/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614711760",
                    "related" =>
                        "https://example.com/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
                ],
            ],
            [
                "id" =>
                    "jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614711761",
                "date_soumission" => 1614711761,
                "tests_réussis" => 2,
                "feedback" => "feedbackTest Java",
                "langage" => "java",
                "code" => "codeTestJava",
                "réussi" => true,
                "sous-type" => "tentativeProg",
                "links" => [
                    "self" =>
                        "https://example.com/tentative/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614711761",
                    "related" =>
                        "https://example.com/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
                ],
            ],
        ];

        $avancementProgTransformer = new AvancementProgTransformer();
        $résultats_obtenus = $avancementProgTransformer->includeTentatives(
            $avancement
        );

        $i = 0;
        foreach ($résultats_obtenus->getData() as $résultat_obtenu) {
            $this->assertEquals(
                $résultats_attendus[$i++],
                $résultats_obtenus
                    ->getTransformer()
                    ->transform($résultat_obtenu)
            );
        }
        $this->assertEquals(2, $i);
    }

    public function test_étant_donné_un_avancement_sans_tentative_lorsquon_inclut_les_tentatives_on_reçoit_un_tableau_vide()
    {
        $_ENV["APP_URL"] = "https://example.com/";

        $avancementProgTransformer = new AvancementProgTransformer();
        $avancement = new AvancementProg(
            "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
            "jdoe"
        );
        $avancement->id =
            "jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24";
        $avancement->tentatives = [];

        $résultat = [];

        $this->assertEquals(
            $résultat,
            $avancementProgTransformer
                ->includeTentatives($avancement)
                ->getData()
        );
    }
}
