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
use progression\dao\DAOFactory;
use Illuminate\Support\Facades\Log;
use progression\domaine\interacteur\ObtenirQuestionProgInt;
use progression\http\transformer\SolutionTransformer;

class SolutionCtl extends Contrôleur
{
    public function get( Request $request, $question ) {
        $solution = null;

        $chemin = base64_decode($question);        

        if ($chemin != null && $chemin != "" ) {
            $questionProgInt = new ObtenirQuestionProgInt( new DAOFactory );
            $question = $questionProgInt->get_question($chemin);
        }

        if ($question != null ) {
            $réponse = $this->item([ "question" => $question, "langage" => $request["langage"] ], new SolutionTransformer );
            
            Log::info("(" . $request->ip() . ") - " . $request->method() . " " . $request->path() . "(" . __CLASS__ . ")");
            return $this->réponse_json($réponse, 200);
        } else {
            Log::warning("(" . $request->ip() . ") - " . $request->method() . " " . $request->path() . "(" . __CLASS__ . ")");
            return $this->réponse_json(['message' => 'Question non trouvée.'], 404);
        }
    }
}

?>