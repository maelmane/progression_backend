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
use progression\domaine\entité\Exécutable;
use PHPUnit\Framework\TestCase;

final class SolutionTransformerTests extends TestCase
{
    public function test_étant_donné_une_solution_instanciée_avec_des_valeurs_lorsquon_récupère_son_transformer_on_obtient_un_objet_json_correspondant()
    {
        $_ENV['APP_URL'] = 'https://example.com';
        $solutionTransformer = new SolutionTransformer();

        $exécutable = new Exécutable("return nb1 + nb2;", "java");

        $json =
            '{"id":"java","langage":"java","code":"return nb1 + nb2;"}';
        $item = $solutionTransformer->transform($exécutable);

        $this->assertEquals($json, json_encode($item, JSON_UNESCAPED_UNICODE));
    }
    public function test_étant_donné_une_solution_null_lorsquon_récupère_son_transformer_on_obtient_un_array_null()
    {
        $solutionTransformer = new SolutionTransformer();
        $question = null;
        $json = '[null]';
        $item = $solutionTransformer->transform($question);

        $this->assertEquals($json, json_encode($item, JSON_UNESCAPED_UNICODE));
    }
}

?>
