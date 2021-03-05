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

class AvancementProgTransformer extends AvancementTransformer
{
    public function includeTentatives($avancement)
    {
        $tentatives = $avancement->réponses;
        foreach ($tentatives as $tentative) {
            $tentative->id =
                "{$avancement->username}/" .
                Encodage::base64_encode_url($avancement->question_uri) .
                "/" .
                $tentative->date_soumission;
            $tentative->links = [
                "related" => $_ENV["APP_URL"] . "tentative/" . $tentative->id,
            ];
        }

        return $this->collection(
            $tentatives,
            new TentativeProgTransformer(),
            "tentative"
        );
    }
}
