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
use progression\http\transformer\{TentativeProgTransformer, TentativeSysTransformer, TentativeBDTransformer};
use progression\domaine\entité\{TentativeProg, TentativeSys, TentativeBD};
use progression\domaine\entité\{QuestionProg, QuestionSys, QuestionBD};
use progression\exceptions\ExécutionException;
use progression\util\Encodage;

class TentativeCtl extends Contrôleur
{
	public function get(Request $request, $username, $question_uri, $timestamp)
	{
		$tentative = null;

		$chemin = Encodage::base64_decode_url($question_uri);

		$tentativeInt = $this->intFactory->getObtenirTentativeInt();
		$tentative = $tentativeInt->get_tentative($username, $chemin, $timestamp);

		if ($tentative != null) {
			$tentative->id = "{$username}/{$question_uri}/{$timestamp}";
		}

		$réponse = null;

		if ($tentative instanceof TentativeProg) {
			$réponse = $this->item($tentative, new TentativeProgTransformer());
		} elseif ($tentative instanceof TentativeSys) {
			$réponse = $this->item($tentative, new TentativeSysTransformer());
		} elseif ($tentative instanceof TentativeBD) {
			$réponse = $this->item($tentative, new TentativeBDTransformer());
		}

		return $this->préparer_réponse($réponse);
	}

	public function post(Request $request, $username, $question_uri)
	{
		$tentative = null;
		$réponse = null;

		$chemin = Encodage::base64_decode_url($question_uri);

		$questionInt = $this->intFactory->getObtenirQuestionInt();
		$question = $questionInt->get_question($chemin);

		if ($question instanceof QuestionProg) {
			$input = $request->only(["langage", "code"]);

			if (count(array_filter($input)) == 2) {
				$tentative = new TentativeProg($input["langage"], $input["code"], (new \DateTime())->getTimestamp());

				$tentativeInt = $this->intFactory->getSoumettreTentativeProgInt();

				try {
					$tentative = $tentativeInt->soumettre_tentative($username, $question, $tentative);
				} catch (ExécutionException $e) {
					return $this->réponse_json(["message" => $e->getMessage()], 503);
				}
			}

			if ($tentative != null) {
				$tentative->id = "{$username}/{$question_uri}/{$tentative->date_soumission}";
				$réponse = $this->item($tentative, new TentativeProgTransformer());
			}
		} elseif ($question instanceof QuestionSys) {
			return $this->réponse_json(["message" => "Question système non implémentée."], 501);
		} elseif ($question instanceof QuestionBD) {
			return $this->réponse_json(["message" => "Question BD non implémentée."], 501);
		}

		return $this->préparer_réponse($réponse);
	}
}
