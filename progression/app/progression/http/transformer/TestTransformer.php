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
use League\Fractal;

class TestTransformer extends Fractal\TransformerAbstract
{
    public $type = "test";

    public function transform(Test $test)
    {
        $data = [
            "id" => $test->id,
            "numéro" => $test->numéro,
            "nom" => $test->nom,
            "entrée" => $test->entrée,
            "sortie_attendue" => $test->sortie_attendue,
            "links" => (isset($test->links) ? $test->links : []) + [
                "self" => "{$_ENV["APP_URL"]}test/{$test->id}",
            ],
        ];

        return $data;
    }
}
