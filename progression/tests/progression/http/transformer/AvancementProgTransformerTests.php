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
use progression\domaine\entité\QuestionProg;
use PHPUnit\Framework\TestCase;

final class AvancementProgTransformerTests extends TestCase
{
    public function test_étant_donné_un_avancement_instancié_avec_des_valeurs_lorsquon_récupère_son_transformer_on_obtient_un_array_d_objets_identique()
    {
        $_ENV['APP_URL'] = 'https://example.com';
        $user_id = 1;
        $question_id = 1;

        $avancementProgTransformer = new AvancementProgTransformer();
        $question = new QuestionProg($question_id);
        $avancement = new AvancementProg($question_id, $user_id);
        
        $résultat = [
            "id" => $user_id . "/" . $question->chemin,
            "user_id" => $avancement->user_id,
            "question_id" => $question->id,
            "état" => 0,
            "réponses" => [],
            "links" => [
                "self" => $_ENV['APP_URL'] . "avancement/" . $avancement->user_id . "/" . $question->id
            ]
        ];

        $this->assertEquals( $résultat, $avancementProgTransformer->transform(["avancement" => $avancement, "question" => $question]) );
    }

    public function test_étant_donné_un_avancement_null_lorsquon_récupère_son_transformer_on_obtient_un_array_null()
    {
        $avancementProgTransformer = new AvancementProgTransformer();

        $avancement = null;

        $json = [null];
        $item = $avancementProgTransformer->transform($avancement);

        $this->assertEquals($json, $item);
    }
}

?>
