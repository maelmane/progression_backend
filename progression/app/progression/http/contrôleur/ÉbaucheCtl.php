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

use progression\http\transformer\ÉbaucheTransformer;

class ÉbaucheCtl extends Contrôleur
{
	public function get($question, $langage)
	{
		$chemin = base64_decode($question);
		$réponse = [null];
		$question = null;

		if ($chemin != null && $chemin != "") {
			$questionProgInt =  $this->intFactory->getObtenirQuestionProgInt();
			$question = $questionProgInt->get_question($chemin);
		}

		if ($question != null) {

			if (array_key_exists($langage, $question->exécutables)) {
				$ébauche = $question->exécutables[$langage];
				$ébauche->id = base64_encode($question->chemin) . "/{$ébauche->lang}";
				$ébauche->links = [
					"related" =>
					$_ENV['APP_URL'] .
						"question/" .
						base64_encode($question->chemin),
				];

				$réponse = $this->item($ébauche, new ÉbaucheTransformer, "ebauche");
			}
		}

		return $this->préparer_réponse($réponse);
	}
}
