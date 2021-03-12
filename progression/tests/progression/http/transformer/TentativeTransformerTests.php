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

use PHPUnit\Framework\TestCase;
use progression\domaine\entité\TentativeProg;

final class TentativeTransformerTests extends TestCase
{
    public function test_étant_donné_une_tentative_instanciée_avec_des_valeurs_lorsquon_récupère_son_transformer_on_obtient_un_tableau_d_objet_correspondant()
    {
        $_ENV["APP_URL"] = "https://example.com/";

        $tentative = new TentativeProg(
            "python",
            "codeTest",
            1614711760,
            2,
            false,
            "feedBackTest"
        );
        $tentative->id =
            "roger/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614711760";
        $tentativeTransformer = new TentativeProgTransformer();
        $résultat = [
            "id" =>
                "roger/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614711760",
            "date_soumission" => 1614711760,
            "sous-type" => "tentativeProg",
            "réussi" => false,
            "tests_réussis" => 2,
            "feedback" => "feedBackTest",
            "langage" => "python",
            "code" => "codeTest",
            "links" => [
                "self" =>
                    "https://example.com/tentative/roger/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614711760",
            ],
        ];

        $this->assertEquals(
            $résultat,
            $tentativeTransformer->transform($tentative)
        );
    }
}
