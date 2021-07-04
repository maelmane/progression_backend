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

use progression\domaine\interacteur\ObtenirQuestionInt;
use progression\http\transformer\ÉbaucheTransformer;
use progression\util\Encodage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DomainException, LengthException, RuntimeException;

class ÉbaucheCtl extends Contrôleur
{
	public function get(Request $request, $question_uri, $langage)
	{
		Log::debug("ÉbaucheCtl.get. Params : ", [$request->all(), $question_uri, $langage]);

		try {
			$question = $this->obtenir_question($question_uri);
			$réponse = $this->valider_et_préparer_réponse($question, $question_uri, $langage);
		} catch (LengthException $erreur) {
			Log::warning("({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ")");
			$réponse = $this->réponse_json(["message" => "Limite de volume dépassé."], 509);
		} catch (RuntimeException $erreur) {
			Log::notice("({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ")");
			$réponse = $this->réponse_json(["message" => "Ressource indisponible sur le serveur distant."], 502);
		} catch (DomainException $erreur) {
			Log::notice("({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ")");
			$réponse = $this->réponse_json(["message" => "Requête intraitable."], 422);
		}

		Log::debug("ÉbaucheCtl.get. Retour : ", [$réponse]);

		return $réponse;
	}

	private function valider_et_préparer_réponse($question, $question_uri, $langage)
	{
		Log::debug("ÉbaucheCtl.valider_et_préparer_réponse. Params : ", [$question, $question_uri, $langage]);
		$ébauche_array = null;

		if ($question != null) {
			$ébauche = $this->préparer_ébauche($question, $question_uri, $langage);
			$ébauche_array = $this->item($ébauche, new ÉbaucheTransformer());
		}

		$réponse = $this->préparer_réponse($ébauche_array);

		Log::debug("ÉbaucheCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);
		return $réponse;
	}

	private function préparer_ébauche($question, $question_uri, $langage)
	{
		Log::debug("ÉbaucheCtl.préparer_ébauche. Params : ", [$question, $question_uri, $langage]);

		$ébauche = null;
		if (array_key_exists($langage, $question->exécutables)) {
			$ébauche = $question->exécutables[$langage];
			$ébauche->id = "$question_uri/{$ébauche->lang}";
			$ébauche->links = [
				"related" => $_ENV["APP_URL"] . "question/" . $question_uri,
			];
		}

		Log::debug("ÉbaucheCtl.préparer_ébauche. Retour : ", [$ébauche]);
		return $ébauche;
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
