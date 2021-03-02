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

use progression\util\Encodage;

class QuestionProgTransformer extends QuestionTransformer
{
    protected $availableIncludes = ["tests", "ebauches"];

    public function includeTests($data_in)
    {
        $question = $data_in["question"];

        foreach ($question->tests as $i => $test) {
            $test->numéro = $i;
            $test->id = Encodage::base64_encode_url($question->chemin) . "/$i";
            $test->links = [
                "related" =>
                    $_ENV["APP_URL"] .
                    "question/" .
                    Encodage::base64_encode_url($question->chemin),
            ];
        }

        return $this->collection(
            $question->tests,
            new TestTransformer(),
            "test"
        );
    }

    //Doit être en minuscules à cause de l'accent (É n'est pas transformé en é)
    public function includeEbauches($data_in)
    {
        $question = $data_in["question"];

        foreach ($question->exécutables as $ébauche) {
            $ébauche->id =
                Encodage::base64_encode_url($question->chemin) . "/{$ébauche->lang}";
            $ébauche->links = [
                "related" =>
                    $_ENV["APP_URL"] .
                    "question/" .
                    Encodage::base64_encode_url($question->chemin),
            ];
        }

        return $this->collection(
            $question->exécutables,
            new ÉbaucheTransformer(),
            "ebauche"
        );
    }
}
