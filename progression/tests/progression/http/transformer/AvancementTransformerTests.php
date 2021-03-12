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

final class AvancementTransformerTests extends TestCase
{
    public function test_étant_donné_un_avancement_instancié_avec_des_valeurs_lorsquon_récupère_son_transformer_on_obtient_un_array_d_objets_identique()
    {
        $_ENV["APP_URL"] = "https://example.com/";

        $avancementProgTransformer = new AvancementTransformer();
        $avancement = new AvancementProg(
            "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
            "jdoe"
        );
        $avancement->id =
            "jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24";

        $résultat = [
            "id" =>
                "jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
            "username" => "jdoe",
            "état" => 0,
            "links" => [
                "self" =>
                    "https://example.com/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
            ],
        ];

        $this->assertEquals(
            $résultat,
            $avancementProgTransformer->transform($avancement)
        );
    }
}
