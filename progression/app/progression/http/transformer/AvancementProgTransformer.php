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

class AvancementProgTransformer extends Fractal\TransformerAbstract
{

    public $type = "AvancementProg";

    protected $availableIncludes = ['tentatives'];

    public function transform($data_in)
    {
        if ($data_in == null) {
            $data_out = [null];
        } else {
            $avancement = $data_in["avancement"];
            $question = $data_in["question"];

            $data_out = [
                'id' => $avancement->user_id . '/' . $question->id,
                'user_id' => $avancement->user_id,
                'état' => $avancement->etat,
                'links'   => [
                    'self' => $_ENV['APP_URL'] . 'avancement/' . $avancement->user_id . '/' . $question->id
                ]
            ];
        }

        return $data_out;
    }

    public function includeTentatives($data_in)
    {
        $tentatives = $data_in["avancement"]->réponses;
        return $this->collection(
            $tentatives,
            new TentativeTransformer(),
            "Tentative"
        );
    }
}
