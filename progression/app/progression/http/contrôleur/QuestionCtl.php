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
use progression\domaine\entité\Question;
use progression\http\transformer\QuestionProgTransformer;
use Illuminate\Support\Facades\Log;

class QuestionCtl extends Contrôleur
{
	public function get(Request $request, $chemin)
	{
		$question = null;

		$chemin = base64_decode($chemin);

		if ($chemin != null && $chemin != "") {
			$questionInt = $this->intFactory->getObtenirQuestionInt();
			$question = $questionInt->get_question($chemin);
			if ($question->type == Question::TYPE_PROG) {
				$questionInt = $this->intFactory->getObtenirQuestionProgInt();
				$question = $questionInt->get_question($chemin);
			} elseif ($question->type == Question::TYPE_SYS) {
				// À implémenter
			} elseif ($question->type == Question::TYPE_BD) {
				// À implémenter
			}
		}

		if ($question->type == Question::TYPE_PROG) {
			$réponse = $this->item(
				["question" => $question, "username" => $request["username"]],
				new QuestionProgTransformer(),
				"question",
			);
			return $this->préparer_réponse($réponse);
		} elseif ($question->type == Question::TYPE_SYS) {
			Log::warning("({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ")");
			return $this->réponse_json(["message" => "Question système non implémentée."], 501);
		} elseif ($question->type == Question::TYPE_BD) {
			Log::warning("({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ")");
			return $this->réponse_json(["message" => "Question BD non implémentée."], 501);
		}
	}
}
