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
use progression\domaine\entité\AvancementProg;
use PHPUnit\Framework\TestCase;

final class AvancementProgTransformerTests extends TestCase
{
    public function test_étant_donné_un_avancement_instancié_avec_des_valeurs_lorsquon_récupère_son_transformer_on_obtient_un_objet_json_correspondant()
    {
        $_ENV['APP_URL'] = 'https://example.org';
        $user_id = 1;
        $question_id = 1;

        $avancementProgTransformer = new AvancementProgTransformer();
        $avancement = new AvancementProg($question_id, $user_id);
        
        $json =
            '{"id":"1\/1","user_id":1,"question_id":1,"etat":0,"réponses":[],"links":[{"rel":"self","self":"https:\/\/example.org\/1\/1"}]}';
        $item = $avancementProgTransformer->transform($avancement);

        $this->assertEquals($json, json_encode($item, JSON_UNESCAPED_UNICODE));
    }
    public function test_étant_donné_un_avancement_null_lorsquon_récupère_son_transformer_on_obtient_un_array_null()
    {
        $questionProgTransformer = new QuestionProgTransformer();
        $question = null;
        $json = '[null]';
        $item = $questionProgTransformer->transform($question);

        $this->assertEquals($json, json_encode($item, JSON_UNESCAPED_UNICODE));
    }
}

?>
