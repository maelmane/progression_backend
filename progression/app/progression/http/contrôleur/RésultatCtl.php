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
use progression\http\transformer\dto\GénériqueDTO;
use progression\domaine\entité\question\QuestionProg;
use progression\domaine\entité\{Résultat, Test, TestProg, TentativeProg};
use progression\domaine\interacteur\{
	ObtenirQuestionInt,
	SoumettreTentativeProgInt,
	SoumettreTentativeIntéracteurException,
	IntéracteurException,
};
use progression\util\Encodage;
use RuntimeException;
use DomainException;
use progression\dao\question\ChargeurException;
use progression\dao\exécuteur\ExécutionException;

class RésultatCtl extends Contrôleur
{
	public function post(Request $request, string $uri): JsonResponse
	{
		Log::debug("RésultatCtl.post. Params : ", [$request->all()]);

		$validation = $this->valider_paramètres($request, $uri);
		if ($validation->fails()) {
			Log::notice(
				"({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ") Paramètres invalides",
			);
			return $this->réponse_json(["erreur" => $validation->errors()], 400);
		}

		$chemin = Encodage::base64_decode_url($uri);

		$question = $this->récupérer_question($chemin);
		if (!$question) {
			$réponse = $this->réponse_json(["erreur" => "Err: 1002. La question " . $chemin . " n'existe pas."], 404);
		} elseif (isset($request->index) && !array_key_exists($request->index, $question->tests)) {
			$réponse = $this->réponse_json(["erreur" => "Err: 1003. L'indice de test n'existe pas."], 400);
		} else {
			$test = isset($request->index) ? $question->tests[$request->index] : $this->construire_test($request);
			$résultats = $this->traiter_post_QuestionProg($request, $chemin, $question, $test);

			if (!$résultats || count($résultats) != 1) {
				$réponse = $this->réponse_json(["erreur" => "Err: 1000. La tentative n'est pas traitable."], 400);
			} else {
				$hash = array_key_first($résultats);
				$résultat = $résultats[$hash];
				if ($test->caché) {
					$résultat = $this->caviarder_résultat($résultat);
				}
				$réponse = $this->valider_et_préparer_réponse($résultat, $hash);
			}
		}

		Log::debug("RésultatCtl.post. Retour : ", [$réponse]);
		return $réponse;
	}

	/**
	 * @return array<string>
	 */
	public static function get_liens(string $hash): array
	{
		$urlBase = Contrôleur::$urlBase;

		return [
			"self" => "{$urlBase}/resultat/{$hash}",
		];
	}

	private function valider_paramètres(Request $request, string $uri)
	{
		$TAILLE_CODE_MAX = (int) getenv("TAILLE_CODE_MAX");

		$validateur = Validator::make(
			[...$request->all(), "uri" => $uri],
			[
				"uri" => [
					"required",
					function ($attribute, $value, $fail) {
						$url = Encodage::base64_decode_url($value);
						if (!$url || Validator::make(["uri" => $url], ["uri" => "url"])->fails()) {
							$fail("Err: 1003. Le champ uri doit être un URL encodé en base64.");
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

	private function récupérer_question(string $chemin): QuestionProg|null
	{
		return (new ObtenirQuestionInt())->get_question($chemin);
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

	private function valider_et_préparer_réponse(Résultat $résultat, string $hash): JsonResponse
	{
		Log::debug("RésulangageltatCtl.valider_et_préparer_réponse. Params : ", [$résultat]);

		$dto = new GénériqueDTO(id: "{$hash}", objet: $résultat, liens: RésultatCtl::get_liens($hash));

		$réponse = $this->item($dto, new RésultatTransformer());

		$réponse = $this->préparer_réponse($réponse);

		Log::debug("RésultatCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);
		return $réponse;
	}

	/**
	 * @return array<Résultat>
	 */
	private function traiter_post_QuestionProg(
		Request $request,
		string $chemin,
		QuestionProg $question,
		Test $test,
	): array|null {
		$tentative = new TentativeProg($request->langage, $request->code, (new \DateTime())->getTimestamp());

		$tentative_résultante = $this->soumettre_tentative($question, $test, $tentative);
		if (!$tentative_résultante) {
			return null;
		}

		return $tentative_résultante->résultats;
	}

	private function soumettre_tentative(
		QuestionProg $question,
		Test $test,
		TentativeProg $tentative,
	): TentativeProg|null {
		$intéracteur = new SoumettreTentativeProgInt();
		return $intéracteur->soumettre_tentative($question, [$test], $tentative);
	}

	private function caviarder_résultat(Résultat $résultat): Résultat
	{
		$résultat->sortie_observée = null;
		$résultat->sortie_erreur = null;

		return $résultat;
	}
}
