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
use progression\domaine\entité\{QuestionProg, QuestionSys, QuestionBD};
use progression\domaine\interacteur\ObtenirQuestionInt;
use progression\http\transformer\QuestionProgTransformer;
use progression\util\Encodage;
use DomainException, LengthException, RuntimeException;

class QuestionCtl extends Contrôleur
{
	public function get(Request $request, $uri)
	{
		Log::debug("QuestionCtl.get. Params : ", [$request->all(), $uri]);

		try {
			$question = $this->obtenir_question($uri);
			$réponse = $this->valider_et_préparer_réponse($question);
		} catch (LengthException $erreur) {
			Log::warning("({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ")");
			$réponse = $this->réponse_json(["message" => "Limite de volume dépassé."], 509);
		} catch (RuntimeException $erreur) {
			Log::notice("({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ")");
			$réponse = $this->réponse_json(["message" => "Ressource indisponible sur le serveur distant."], 502);
		} catch (DomainException $erreur) {
			Log::notice("({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ")");
			$réponse = $this->réponse_json(["message" => "Requête intraitable."], 400);
		}

		Log::debug("QuestionCtl.get. Retour : ", [$réponse]);

		return $réponse;
	}

	private function obtenir_question($question_uri)
	{
		Log::debug("QuestionCtl.obtenir_question. Params : ", [$question_uri]);

		$chemin = Encodage::base64_decode_url($question_uri);
		$questionInt = new ObtenirQuestionInt();
		$question = $questionInt->get_question($chemin);

		Log::debug("Question.Ctl.obtenir_question. Retour : ", [$question]);
		return $question;
	}

	private function valider_et_préparer_réponse($question)
	{
		Log::debug("QuestionCtl.valider_et_préparer_réponse. Params : ", [$question]);

		if ($question instanceof QuestionProg) {
			$réponse_array = $this->item(["question" => $question], new QuestionProgTransformer());
			$réponse = $this->préparer_réponse($réponse_array);
		} elseif ($question instanceof QuestionSys || $question instanceof QuestionBD) {
			Log::notice("Type de question non implémentée : " . get_class($question));
			$réponse = $this->réponse_json(["message" => "Type de question non implémentée."], 501);
		} else {
			$réponse = $this->préparer_réponse($question);
		}

		Log::debug("QuestionCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);
		return $réponse;
	}
}
