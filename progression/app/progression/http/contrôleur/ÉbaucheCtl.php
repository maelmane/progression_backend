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

use progression\domaine\interacteur\{ObtenirQuestionInt, IntéracteurException};
use progression\http\transformer\ÉbaucheTransformer;
use progression\http\transformer\dto\GénériqueDTO;
use progression\util\Encodage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ÉbaucheCtl extends Contrôleur
{
	public function get(Request $request, $question_uri, $langage)
	{
		Log::debug("ÉbaucheCtl.get. Params : ", [$request->all(), $question_uri, $langage]);

		$réponse = null;
		$question = $this->obtenir_question($question_uri);
		$réponse = $this->valider_et_préparer_réponse($question, $question_uri, $langage);

		Log::debug("ÉbaucheCtl.get. Retour : ", [$réponse]);

		return $réponse;
	}

	/**
	 * @return array<string>
	 */
	public static function get_liens(string $question_uri, string $lang): array
	{
		$urlBase = Contrôleur::$urlBase;

		return [
			"self" => "{$urlBase}/ebauche/{$question_uri}/{$lang}",
			"question" => "{$urlBase}/question/{$question_uri}",
		];
	}

	private function valider_et_préparer_réponse($question, $question_uri, $langage)
	{
		Log::debug("ÉbaucheCtl.valider_et_préparer_réponse. Params : ", [$question, $question_uri, $langage]);
		$réponse = null;

		if ($question != null && array_key_exists($langage, $question->exécutables)) {
			$ébauche = $question->exécutables[$langage];
			$dto = new GénériqueDTO(
				id: "{$question_uri}/{$langage}",
				objet: $ébauche,
				liens: ÉbaucheCtl::get_liens($question_uri, $langage),
			);

			$réponse = $this->item($dto, new ÉbaucheTransformer());
		}

		$réponse = $this->préparer_réponse($réponse);

		Log::debug("ÉbaucheCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);
		return $réponse;
	}

	private function obtenir_question($question_uri)
	{
		Log::debug("ÉbaucheCtl.obtenir_question. Params : ", [$question_uri]);

		$chemin = Encodage::base64_decode_url($question_uri);
		$questionInt = new ObtenirQuestionInt();
		$question = $questionInt->get_question($chemin);

		Log::debug("ÉbaucheCtl.obtenir_question. Retour : ", [$question]);
		return $question;
	}
}
