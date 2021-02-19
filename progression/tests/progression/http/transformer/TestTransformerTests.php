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
use progression\domaine\entité\Test;
use PHPUnit\Framework\TestCase;

final class TestTransformerTests extends TestCase
{
    public function test_étant_donné_un_test_instanciée_avec_des_valeurs_lorsquon_récupère_son_transformer_on_obtient_un_objet_json_correspondant()
    {
        $_ENV['APP_URL'] = 'https://example.com';
        $testTransformer = new TestTransformer();
        $test = new Test("appeler_une_fonction", "21\n21\n", "42");
        $test->numéro = 0;
        $json =
            '{"id":0,"nom":"appeler_une_fonction","entrée":"21\n21\n","sortie":"42"}';
        $item = $testTransformer->transform($test);

        $this->assertEquals($json, json_encode($item, JSON_UNESCAPED_UNICODE));
    }
    public function test_étant_donné_un_test_null_lorsquon_récupère_son_transformer_on_obtient_un_array_null()
    {
        $testTransformer = new TestTransformer();
        $test = null;
        $json = '[null]';
        $item = $testTransformer->transform($test);

        $this->assertEquals($json, json_encode($item, JSON_UNESCAPED_UNICODE));
    }
}

?>
