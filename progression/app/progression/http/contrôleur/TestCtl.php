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

use progression\http\transformer\{TestProgTransformer, TestSysTransformer};
use progression\domaine\interacteur\ObtenirQuestionInt;
use progression\domaine\entité\{QuestionProg, QuestionSys};
use progression\util\Encodage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DomainException, LengthException, RuntimeException;

class TestCtl extends Contrôleur
{
	public function get(Request $request, $question_uri, $numero)
	{
		Log::debug("TestCtl.get. Params : ", [$request->all(), $question_uri, $numero]);

		try {
			$question = $this->obtenir_question($question_uri);
			$réponse = $this->valider_et_préparer_réponse($question, $question_uri, $numero);
		} catch (LengthException $erreur) {
			Log::error("({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ")");
			return $this->réponse_json(["message" => "Limite de volume dépassé."], 509);
		} catch (RuntimeException $erreur) {
			Log::error("({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ")");
			return $this->réponse_json(["message" => "Ressource indisponible sur le serveur distant."], 502);
		} catch (DomainException $erreur) {
			Log::error("({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ")");
			return $this->réponse_json(["message" => "Requête intraitable."], 400);
		}

		Log::debug("TestCtl.get. Retour : ", [$réponse]);

		return $réponse;
	}

	private function valider_et_préparer_réponse($question, $question_uri, $numero)
	{
		Log::debug("TestCtl.valider_et_préparer_réponse. Params : ", [$question, $question_uri, $numero]);
		$test_array = null;

		if ($question != null) {
			$test = $this->préparer_test($question, $question_uri, $numero);
			if ($question instanceof QuestionProg) {
				$test_array = $this->item($test, new TestProgTransformer());
			}

			if ($question instanceof QuestionSys) {
				$test_array = $this->item($test, new TestSysTransformer());
			}
		}

		$réponse = $this->préparer_réponse($test_array);

		Log::debug("TestCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);
		return $réponse;
	}

	private function préparer_test($question, $question_uri, $numero)
	{
		Log::debug("TestCtl.préparer_test. Params : ", [$question, $question_uri, $numero]);

		$test = null;
		if (array_key_exists($numero, $question->tests)) {
			$test = $question->tests[$numero];
			$test->id = "$question_uri/$numero";
			$test->links = [
				"related" => $_ENV["APP_URL"] . "question/" . $question_uri,
			];

			if ($question instanceof QuestionProg) {
				$réponse = $this->item($test, new TestProgTransformer());
			}

			if ($question instanceof QuestionSys) {
				$réponse = $this->item($test, new TestSysTransformer());
			}
		}

		Log::debug("TestCtl.préparer_test. Retour : ", [$test]);
		return $test;
	}

	private function obtenir_question($question_uri)
	{
		Log::debug("TestCtl.obtenir_question. Params : ", [$question_uri]);

		$chemin = Encodage::base64_decode_url($question_uri);
		$questionInt = new ObtenirQuestionInt();
		$question = $questionInt->get_question($chemin);

		Log::debug("TestCtl.obtenir_question. Retour : ", [$question]);
		return $question;
	}
}
