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
use progression\domaine\interacteur\ObtenirQuestionProgInt;
use progression\dao\DAOFactory;
use progression\http\transformer\QuestionProgTransformer;
use League\Fractal\Resource\Item;
use Illuminate\Support\Facades\Log;

class QuestionProgCtl extends Contrôleur
{
    public function get( Request $request, $id ) {
        $question = null;

        if ($id != null && $id != "" ) {
            $questionInt = new ObtenirQuestionProgInt(new DAOFactory);
            $question = $questionInt->get_question($id);
        }

        if ($question != null) {
            $réponse = $this->item($question, new QuestionProgTransformer);
            
            Log::info("(" . $request->ip() . ") - " . $request->method() . " " . $request->path() . "(" . __CLASS__ . ")");
            return $this->réponse_json($réponse, 200);
        } else {
            Log::warning("(" . $request->ip() . ") - " . $request->method() . " " . $request->path() . "(" . __CLASS__ . ")");
            return $this->réponse_json(['message' => 'Question non trouvée.'], 404);
        }
    }
}
