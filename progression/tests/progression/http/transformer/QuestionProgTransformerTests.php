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
use progression\domaine\entité\QuestionProg;
use PHPUnit\Framework\TestCase;
use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\Resource\Item;

final class QuestionProgTransformerTests extends TestCase{
  public function test_étant_donné_une_questionprog_instanciée_avec_des_valeurs_lorsquon_récupère_son_transformer_on_obtient_un_objet_json_correspondant(){
    $_ENV['APP_URL']='https://progression.dti.crosemont.quebec';
    $questionProgTransformer = new QuestionProgTransformer();
    $question = new QuestionProg();
    $question->id = 1;
    $question->titre = "titreTest";
    $question->description = "descriptionTest";
    $question->enonce = "énoncéTest";
    $json = '{"id":null,"nom":null,"titre":"titreTest","description":"descriptionTest","énoncé":"énoncéTest","type":"QuestionProg","links":[{"rel":"self","self":"https:\/\/progression.dti.crosemont.quebec"}]}';
    $item = $questionProgTransformer->transform($question);

    $this->assertEquals(
      $json,
      json_encode(
                $item,
                JSON_UNESCAPED_UNICODE
      )
    );
  }
  public function test_étant_donné_une_questionprog_null_lorsquon_récupère_son_transformer_on_obtient_un_array_null(){
    $questionProgTransformer = new QuestionProgTransformer();
    $question = null;
    $json = '[null]';
    $item = $questionProgTransformer->transform($question);

    $this->assertEquals(
      $json,
      json_encode(
                $item,
                JSON_UNESCAPED_UNICODE
      )
    );
  }
}

?>
