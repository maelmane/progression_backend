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
use progression\domaine\interacteur\ObtenirQuestionInt;
use progression\domaine\interacteur\ObtenirQuestionProgInt;
use progression\dao\DAOFactory;
use Illuminate\Support\Facades\Log;
use progression\domaine\entité\Question;
use progression\http\transformer\QuestionProgTransformer;

class QuestionCtl extends Contrôleur
{
    public function get(Request $request, $chemin)
    {
        $question = null;

        $chemin = base64_decode($chemin);

        if ($chemin != null && $chemin != "") {
            $questionInt = new ObtenirQuestionInt(new DAOFactory);
            $question = $questionInt->get_question($chemin);
            if ($question->type == Question::TYPE_PROG) {
                $questionInt = new ObtenirQuestionProgInt(new DAOFactory);
                $question = $questionInt->get_question($chemin);
            } else if ($question->type == Question::TYPE_SYS) {
            } // À implémenter
            else if ($question->type == Question::TYPE_BD) {
            } // À implémenter
        }

        if ($question != null) {
            if ($question->type == Question::TYPE_PROG) {
                $réponse = $this->item(["question" => $question, "username" => $request["username"]], new QuestionProgTransformer);
            } else if ($question->type == Question::TYPE_SYS) {
                Log::error("(" . $request->ip() . ") - " . $request->method() . " " . $request->path() . "(" . __CLASS__ . ")");
                return $this->réponse_json(['message' => 'Question système non implémentée.'], 501);
            } else if ($question->type == Question::TYPE_BD) {
                Log::error("(" . $request->ip() . ") - " . $request->method() . " " . $request->path() . "(" . __CLASS__ . ")");
                return $this->réponse_json(['message' => 'Question BD non implémentée.'], 501);
            }

            Log::info("(" . $request->ip() . ") - " . $request->method() . " " . $request->path() . "(" . __CLASS__ . ")");
            return $this->réponse_json($réponse, 200);
        } else {
            Log::warning("(" . $request->ip() . ") - " . $request->method() . " " . $request->path() . "(" . __CLASS__ . ")");
            return $this->réponse_json(['message' => 'Question non trouvée.'], 404);
        }
    }
}
