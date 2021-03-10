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
    public function test_étant_donné_une_tentative_instanciée_avec_des_valeurs_lorsquon_récupère_son_transformer_on_obtient_un_objet_json_correspondant()
    {
        $tentative = new TentativeProg(10, "codeTest", 1614711760, "testsRéussisTest", "feedBackTest");
        $tentative->id = "roger/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614711760";
        $tentativeTransformer = new TentativeProgTransformer();
        $username = "roger";
        $résultat = [
            "id" => "{$username}/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24" .
                "/" .
                $tentative->date_soumission,
            'date_soumission' => $tentative->date_soumission,
            'tests_réussis' => $tentative->tests_réussis,
            'feedback' => $tentative->feedback,
            'langage' => $tentative->langage,
            'code' => $tentative->code,
            "links" => [
                "self" => "{$_ENV["APP_URL"]}tentative/{$username}/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/{$tentative->date_soumission}",
            ]
        ];

        $this->assertEquals($résultat, $tentativeTransformer->transform($tentative));
    }
}
