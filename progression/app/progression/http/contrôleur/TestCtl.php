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
use progression\http\transformer\dto\GénériqueDTO;
use progression\domaine\interacteur\{ObtenirQuestionInt, IntéracteurException};
use progression\domaine\entité\question\{Question, QuestionProg, QuestionSys};
use progression\util\Encodage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestCtl extends Contrôleur
{
	public function get(Request $request, $question_uri, $numero)
	{
		Log::debug("TestCtl.get. Params : ", [$request->all(), $question_uri, $numero]);

		$réponse = null;
		$question = $this->obtenir_question($question_uri);
		$réponse = $this->valider_et_préparer_réponse($question, $question_uri, $numero);
		Log::debug("TestCtl.get. Retour : ", [$réponse]);

		return $réponse;
	}

	/**
	 * @return array<string>
	 */
	public static function get_liens(string $question_uri, int $numéro): array
	{
		$urlBase = Contrôleur::$urlBase;

		$liens = [
			"self" => "{$urlBase}/test/{$question_uri}/{$numéro}",
			"question" => "{$urlBase}/question/{$question_uri}",
		];

		return $liens;
	}

	private function valider_et_préparer_réponse(Question $question, string $question_uri, int $numéro)
	{
		Log::debug("TestCtl.valider_et_préparer_réponse. Params : ", [$question, $question_uri, $numéro]);
		$test_array = null;

		if ($question != null) {
			$test = $this->préparer_test($question, $numéro);

			if ($test) {
				$dto = new GénériqueDTO(
					id: "$question_uri/$numéro",
					objet: $test,
					liens: TestCtl::get_liens($question_uri, $numéro),
				);

				if ($question instanceof QuestionProg) {
					$test_array = $this->item($dto, new TestProgTransformer());
				}

				if ($question instanceof QuestionSys) {
					$test_array = $this->item($dto, new TestSysTransformer());
				}
			}
		}

		$réponse = $this->préparer_réponse($test_array);

		Log::debug("TestCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);
		return $réponse;
	}

	private function préparer_test(Question $question, int $numéro)
	{
		Log::debug("TestCtl.préparer_test. Params : ", [$question, $numéro]);

		$test = null;
		if (array_key_exists($numéro, $question->tests)) {
			$test = $question->tests[$numéro];
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
