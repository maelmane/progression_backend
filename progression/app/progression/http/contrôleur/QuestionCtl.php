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
use progression\http\transformer\{QuestionProgTransformer, QuestionSysTransformer};
use progression\util\Encodage;
use progression\dao\question\ChargeurException;
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
			Log::warning(
				"({$request->ip()}) - {$request->method()} {$request->path()} (" .
					__CLASS__ .
					") ERR: {$erreur->getMessage()}",
			);
			$réponse = $this->réponse_json(["erreur" => "Limite de volume dépassé."], 413);
		} catch (RuntimeException $erreur) {
			Log::notice(
				"({$request->ip()}) - {$request->method()} {$request->path()} (" .
					__CLASS__ .
					") ERR: {$erreur->getMessage()}",
			);
			$réponse = $this->réponse_json(["erreur" => "Ressource indisponible sur le serveur distant."], 502);
		} catch (DomainException $erreur) {
			Log::notice(
				"({$request->ip()}) - {$request->method()} {$request->path()} (" .
					__CLASS__ .
					") ERR: {$erreur->getMessage()}",
			);
			$réponse = $this->réponse_json(["erreur" => "Requête intraitable."], 400);
		} catch (ChargeurException $erreur) {
			Log::notice(
				"({$request->ip()}) - {$request->method()} {$request->path()} (" .
					__CLASS__ .
					") ERR: {$erreur->getMessage()}",
			);

			$réponse = $this->réponse_json(["erreur" => "Question indisponible"], 400);
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
			$réponse_array = $this->item(["question" => $question], new QuestionSysTransformer());
			$réponse = $this->préparer_réponse($réponse_array);
		} else {
			$réponse = $this->réponse_json(["erreur" => "Type de question inconnu."], 400);
		}

		Log::debug("QuestionCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);
		return $réponse;
	}
}
