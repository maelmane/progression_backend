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
use progression\domaine\entité\Test;
use PHPUnit\Framework\TestCase;
use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\Resource\Item;

final class QuestionTransformerTests extends TestCase{
    public function test_étant_donné_une_question_instanciée_avec_tests_lorsquon_récupère_son_transformer_on_obtient_un_objet_json_correspondant(){
        $testTest1 = new Test("nomTest1", "stdinTest1", "solutionTest1");
        $question = new QuestionProg();
        $question->id = 1;
        $question->titre = "titreTest";
        $question->description = "descriptionTest";
        $question->enonce = "énoncéTest";
        $question->tests = array($testTest1);

        $json = '{"data":{"type":"QuestionProg","id":"1","attributes":{"titre":"titreTest","description":"descriptionTest","énoncé":"énoncéTest","type_de_question":"QuestionProg"},"links":{"self":"https:\/\/progression.dti.crosemont.quebec\/QuestionProg\/1","0":{"rel":"self","self":"https:\/\/progression.dti.crosemont.quebec\/api\/v1\/question\/"}},"relationships":{"Tests":{"links":{"self":"https:\/\/progression.dti.crosemont.quebec\/QuestionProg\/1\/relationships\/Tests","related":"https:\/\/progression.dti.crosemont.quebec\/QuestionProg\/1\/Tests"},"data":[{"type":"Test","id":"0"}]}}},"included":[{"type":"Test","id":"0","attributes":{"nom":"nomTest1","entrée":"stdinTest1","sortie":"solutionTest1"},"links":{"self":"https:\/\/progression.dti.crosemont.quebec\/Test\/0"}}]}';

        $tableau = $this->item($question, new QuestionTransformer);

        $this->assertEquals($json, json_encode($tableau, JSON_UNESCAPED_UNICODE));
    }

    protected function getFractalManager()
    {
        $manager = new Manager();
        $manager->setSerializer(new JsonApiSerializer("https://progression.dti.crosemont.quebec")); //À CHANGER. Dans .env? est-ce qu'on peut le trouver automatiquement?
        $manager->parseIncludes("Tests");
        return $manager;
    }

    public function item($data, $transformer, $resourceKey = null)
    {
        $manager = $this->getFractalManager();
        $resource = new Item($data, $transformer, $transformer->type);
        return $manager->createData($resource)->toArray();
    }
}

?>
