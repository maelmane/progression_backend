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

namespace progression\http\contrôleur;

use Illuminate\Http\Request;
use progression\domaine\interacteur\ObtenirAvancementProgInt;
use progression\dao\DAOFactory;
use progression\http\transformer\AvancementProgTransformer;
use Illuminate\Support\Facades\Log;

class AvancementProgCtl extends Contrôleur
{
    public function get( Request $request, $username, $question ) {
        $avancement = null;

        if ($question != null && $question != "" ) {
            $avancementProgInt = new ObtenirAvancementProgInt(new DAOFactory, $username);
            $avancement = $avancementProgInt->get_avancement($question);
        }

        if ($question != null) {
            $réponse = $this->item($avancement, new AvancementProgTransformer);
            
            Log::info("(" . $request->ip() . ") - " . $request->method() . " " . $request->path() . "(" . __CLASS__ . ")");
            return $this->réponse_json($réponse, 200);
        } else {
            Log::warning("(" . $request->ip() . ") - " . $request->method() . " " . $request->path() . "(" . __CLASS__ . ")");
            return $this->réponse_json(['message' => 'Avancement non trouvé.'], 404);
        }
    }
}
