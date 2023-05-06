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
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use progression\http\transformer\RésultatTransformer;
use progression\domaine\entité\{QuestionProg, Résultat, TestProg, TentativeProg};
use progression\domaine\interacteur\{ObtenirQuestionInt, SoumettreTentativeProgInt};
use progression\util\Encodage;
use RuntimeException;
use DomainException;
use progression\dao\question\ChargeurException;
use progression\dao\exécuteur\ExécutionException;

class RésultatCtl extends Contrôleur
{
	public function put(Request $request): JsonResponse
	{
		Log::debug("RésultatCtl.put. Params : ", [$request->all()]);

		$validation = $this->valider_paramètres($request);
		if ($validation->fails()) {
			Log::notice(
				"({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ") Paramètres invalides",
			);
			return $this->réponse_json(["erreur" => $validation->errors()], 400);
		}

		$chemin = Encodage::base64_decode_url($request->question_uri);

		try {
			$question = $this->récupérer_question($chemin);

			$réponse = $this->traiter_put_QuestionProg($request, $chemin, $question);
		} catch (ContrôleurException $erreur) {
			Log::notice(
				"({$request->ip()}) - {$request->method()} {$request->path()} (" .
					__CLASS__ .
					")
				{$erreur->getMessage()}",
			);
			return $this->réponse_json(["erreur" => $erreur->getMessage()], $erreur->getCode());
		}

		$réponse = $this->valider_et_préparer_réponse($réponse);

		Log::debug("RésultatCtl.put. Retour : ", [$réponse]);

		return $réponse;
	}

	private function valider_paramètres(Request $request)
	{
		$TAILLE_CODE_MAX = (int) $_ENV["TAILLE_CODE_MAX"];

		$validateur = Validator::make(
			$request->all(),
			[
				"question_uri" => [
					"required",
					function ($attribute, $value, $fail) {
						$url = Encodage::base64_decode_url($value);
						if (!$url || Validator::make(["question_uri" => $url], ["question_uri" => "url"])->fails()) {
							$fail("Err: 1003. Le champ question_uri doit être un URL encodé en base64.");
						}
					},
				],
				"code" => "required|string|between:0,$TAILLE_CODE_MAX",
				"langage" => "required|string",
				"test" => "required_without:index",
				"index" => "integer",
			],
			[
				"required" => "Err: 1004. Le champ :attribute est obligatoire.",
				"code.between" =>
					"Err: 1002. Le code soumis " . mb_strlen($request->code) . " > $TAILLE_CODE_MAX caractères.",
				"question_uri.required" => "Err: 1004. Le champ question_uri est obligatoire.",
				"test.required_without" => "Err: 1004. Le champ test est obligatoire lorsque index n'est pas présent.",
			],
		);

		return $validateur;
	}

	private function récupérer_question(string $chemin): QuestionProg
	{
		$questionInt = new ObtenirQuestionInt();

		try {
			return $questionInt->get_question($chemin) ??
				throw new ContrôleurException("Err: 1002. La question $chemin n'existe pas.", 400);
		} catch (RuntimeException $erreur) {
			throw new ContrôleurException($erreur->getMessage(), 502);
		} catch (DomainException $erreur) {
			throw new ContrôleurException($erreur->getMessage(), 400);
		} catch (ChargeurException $erreur) {
			throw new ContrôleurException($erreur->getMessage(), 400);
		}
	}

	private function construire_test(Request $request): TestProg
	{
		return new TestProg(
			nom: $request->test["nom"] ?? "",
			sortie_attendue: $request->test["sortie_attendue"] ?? "",
			entrée: $request->test["entrée"] ?? null,
			params: $request->test["params"] ?? null,
		);
	}

	private function valider_et_préparer_réponse(Résultat|null $résultat): JsonResponse
	{
		Log::debug("RésulangageltatCtl.valider_et_préparer_réponse. Params : ", [$résultat]);

		if ($résultat) {
			$réponse = $this->item($résultat, new RésultatTransformer());
		} else {
			$réponse = null;
		}

		$réponse = $this->préparer_réponse($réponse);

		Log::debug("RésultatCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);
		return $réponse;
	}

	private function traiter_put_QuestionProg(Request $request, string $chemin, QuestionProg $question): Résultat|null
	{
		if (!isset($request->index)) {
			$test = $this->construire_test($request);
		} else {
			$test = $question->tests[$request->index] ?? throw new ContrôleurException("Indice de test invalide", 400);
		}

		$tentative = new TentativeProg($request->langage, $request->code, (new \DateTime())->getTimestamp());

		$tentative_résultante = $this->soumettre_tentative($question, $test, $tentative);

		$hash = array_key_first($tentative_résultante->résultats);
		$résultat = $tentative_résultante->résultats[$hash];
		$résultat->id = $hash;

		return $résultat;
	}

	private function soumettre_tentative(
		QuestionProg $question,
		TestProg $test,
		TentativeProg $tentative
	): TentativeProg {
		try {
			$intéracteur = new SoumettreTentativeProgInt();
			$résultat = $intéracteur->soumettre_tentative($question, [$test], $tentative);
		} catch (ExécutionException $e) {
			if ($e->getCode() >= 500) {
				Log::error($e->getMessage());
				if ($e->getPrevious()) {
					Log::error($e->getPrevious()->getMessage());
				}
				throw new ContrôleurException("Service non disponible.", 503);
			} else {
				throw new ContrôleurException($e->getMessage(), $e->getCode());
			}
		}

		if ($résultat == null) {
			throw new ContrôleurException("Requête intraitable.", 400);
		}
		return $résultat;
	}
}
