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

use League\Fractal;

class ÉbaucheTransformer extends Fractal\TransformerAbstract
{
    public $type = "Ébauche";

    public function transform($data_in)
    {
        if ($data_in == null) {
            $data_out = [null];
        } else {
            $question = $data_in["question"];
            $langage = $data_in["langage"];

            $chemin_encodé = base64_encode($question->chemin);
            $ébauches = $question->exécutables;

            $data_out = [
                'id' => $chemin_encodé . "/" . $langage,
                'langage' => $langage,
                'code' => $ébauches[$langage]->code_exec,
                'links' => [
                    'self' => $_ENV['APP_URL'] . "solution/" . $chemin_encodé . "/" . $langage,
                    'question' =>
                    $_ENV['APP_URL'] .
                        "question/" .
                        $chemin_encodé,
                ],
            ];
        }

        return $data_out;
    }
}
