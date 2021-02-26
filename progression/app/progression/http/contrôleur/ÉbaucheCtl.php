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
use Illuminate\Support\Facades\Log;
use progression\http\transformer\ÉbaucheTransformer;

class ÉbaucheCtl extends Contrôleur
{
	public function get(Request $request, $question, $langage)
	{
		$chemin = base64_decode($question);
		$question = null;
		$réponse = null;

		if ($chemin != null && $chemin != "") {
			$questionProgInt =  $this->intFactory->getObtenirQuestionProgInt();
			$question = $questionProgInt->get_question($chemin);
		}

		if (!array_key_exists($langage, $question->exécutables)) {
			Log::warning("(" . $request->ip() . ") - " . $request->method() . " " . $request->path() . "(" . __CLASS__ . ")");
			return $this->réponse_json(['message' => 'Langage inexistant.'], 404);
		}

		if ($question != null) {
			$ébauche = $question->exécutables[$langage];
			$ébauche->id = base64_encode($question->chemin) . "/{$ébauche->lang}";
			$ébauche->links = [
				"related" =>
				$_ENV['APP_URL'] .
					"question/" .
					base64_encode($question->chemin),
			];
			$réponse = $this->item($ébauche, new ÉbaucheTransformer, "ébauche");
		}

		return $this->préparer_réponse($réponse);
	}
}
