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

use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use progression\http\transformer\{TentativeProgTransformer, TentativeSysTransformer, TentativeBDTransformer};
use progression\domaine\interacteur\{
	ObtenirAvancementInt,
	ObtenirTentativeInt,
	ObtenirQuestionInt,
	SauvegarderAvancementInt,
	SauvegarderTentativeInt,
	SoumettreTentativeProgInt,
	SoumettreTentativeSysInt,
};
use progression\domaine\entité\{Avancement, Tentative, TentativeProg, TentativeSys, TentativeBD};
use progression\domaine\entité\{Question, QuestionProg, QuestionSys, QuestionBD};
use progression\domaine\entité\TestProg;
use progression\dao\exécuteur\ExécutionException;
use progression\dao\question\ChargeurException;
use progression\util\Encodage;
use DomainException, LengthException, RuntimeException;

class TentativeCtl extends Contrôleur
{
	public function get(Request $request, string $username, string $question_uri, int $timestamp): JsonResponse
	{
		$tentative = $this->obtenir_tentative($username, $question_uri, $timestamp);

		if ($tentative != null) {
			$tentative->id = "$timestamp";
		}

		$réponse = null;

		if ($tentative instanceof TentativeProg) {
			$réponse = $this->item(
				$tentative,
				new TentativeProgTransformer("$username/" . (string) $request->question_uri),
			);
		} elseif ($tentative instanceof TentativeSys) {
			$réponse = $this->item($tentative, new TentativeSysTransformer("$username/{$request->question_uri}"));
		} elseif ($tentative instanceof TentativeBD) {
			$réponse = $this->item($tentative, new TentativeBDTransformer("$username/{$request->question_uri}"));
		}

		return $this->préparer_réponse($réponse);
	}

	public function post(Request $request, $username, $question_uri)
	{
		Log::debug("TentativeCtl.post. Params : ", [$request->all(), $username]);

		$chemin = Encodage::base64_decode_url($question_uri);

		try {
			$question = $this->récupérer_question($chemin);

			if ($question instanceof QuestionProg) {
				$validation = $this->valider_paramètres_prog($request);
				if ($validation->fails()) {
					Log::notice(
						"({$request->ip()}) - {$request->method()} {$request->path()} (" .
							__CLASS__ .
							") Paramètres invalides",
					);
					return $this->réponse_json(["erreur" => $validation->errors()], 400);
				}
				$réponse = $this->traiter_post_QuestionProg($request, $username, $chemin, $question);
			} elseif ($question instanceof QuestionSys) {
				$réponse = $this->traiter_post_QuestionSys($request, $username, $chemin, $question);
			} else {
				Log::notice("({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ")");
				return $this->réponse_json(["erreur" => "Question de type non implémentée."], 501);
			}
		} catch (ContrôleurException $erreur) {
			Log::notice(
				"({$request->ip()}) - {$request->method()} {$request->path()} (" .
					__CLASS__ .
					")
				{$erreur->getMessage()}",
			);

			return $this->réponse_json(["erreur" => $erreur->getMessage()], $erreur->getCode());
		}

		Log::debug("TentativeCtl.post. Retour : ", [$réponse]);

		return $réponse;
	}

	private function obtenir_tentative(string $username, string $question_uri, int $timestamp): Tentative|null
	{
		Log::debug("TentativeCtl.obtenir_tentative. Params : ", [$username, $question_uri, $timestamp]);

		$chemin = Encodage::base64_decode_url($question_uri);

		$tentativeInt = new ObtenirTentativeInt();
		$tentative = $tentativeInt->get_tentative($username, $chemin, $timestamp, $this->get_includes());

		Log::debug("TentativeCtl.obtenir_tentative. Retour : ", [$tentative]);
		return $tentative;
	}

	private function traiter_post_QuestionProg(Request $request, $username, $chemin, $question)
	{
		$tests = !empty($request->test)
			? [
				$this->construire_test(
					isset($request->index)
						? $question->tests[$request->index]
						: new TestProg($request->test["nom"] ?? "", ""),
					$request->test["entrée"] ?? null,
					$request->test["params"] ?? null,
					$request->test["sortie_attendue"] ?? null,
				),
			]
			: $question->tests;

		$tentative = new TentativeProg($request->langage, $request->code, (new \DateTime())->getTimestamp());

		$tentative_résultante = $this->soumettre_tentative_prog($username, $question, $tests, $tentative);
		if (!$tentative_résultante) {
			return $this->réponse_json(["erreur" => "Tentative intraitable."], 400);
		}

		if (empty($request->test)) {
			$this->sauvegarder_tentative_et_avancement($username, $chemin, $question, $tentative_résultante);
		}

		$tentative_résultante->id = $tentative->date_soumission;
		$réponse = $this->item($tentative_résultante, new TentativeProgTransformer("$username/$request->question_uri"));

		return $this->préparer_réponse($réponse);
	}

	private function traiter_post_QuestionSys(Request $request, $username, $chemin, $question)
	{
		$conteneur = $request->conteneur ?? $this->récupérer_conteneur($username, $chemin);

		$tentative = new TentativeSys(["id" => $conteneur], $request->réponse, (new \DateTime())->getTimestamp());

		$tentative_résultante = $this->soumettre_tentative_sys($username, $question, $question->tests, $tentative);
		if (!$tentative_résultante) {
			return $this->réponse_json(["erreur" => "Tentative intraitable."], 400);
		}

		$this->sauvegarder_tentative_et_avancement($username, $chemin, $question, $tentative_résultante);

		$tentative_résultante->id = $tentative->date_soumission;
		$réponse = $this->item($tentative, new TentativeSysTransformer("$username/$request->question_uri"));

		return $this->préparer_réponse($réponse);
	}

	private function valider_paramètres_prog($request)
	{
		$TAILLE_CODE_MAX = (int) $_ENV["TAILLE_CODE_MAX"];

		return Validator::make(
			$request->all(),
			[
				"langage" => "required|string",
				"code" => "required|string|between:0,$TAILLE_CODE_MAX",
			],
			[
				"required" => "Le champ :attribute est obligatoire.",
				"code.between" => "Le code soumis " . mb_strlen($request->code) . " > :max caractères.",
			],
		);
	}

	private function récupérer_question($chemin)
	{
		$questionInt = new ObtenirQuestionInt();

		try {
			return $questionInt->get_question($chemin);
		} catch (RuntimeException $erreur) {
			throw new ContrôleurException($erreur->getMessage(), 502);
		} catch (DomainException $erreur) {
			throw new ContrôleurException($erreur->getMessage(), 400);
		} catch (ChargeurException $erreur) {
			throw new ContrôleurException($erreur->getMessage(), 400);
		}
	}

	private function construire_test($test, string|null $entrée, string|null $params, string|null $sortie_attendue)
	{
		if ($entrée !== null) {
			$test->entrée = $entrée;
		}
		if ($params !== null) {
			$test->params = $params;
		}
		if ($sortie_attendue !== null) {
			$test->sortie_attendue = $sortie_attendue;
		}

		return $test;
	}

	private function soumettre_tentative_prog($username, $question, $tests, $tentative)
	{
		return $this->soumettre_tentative($username, $question, $tests, $tentative, new SoumettreTentativeProgInt());
	}
	private function soumettre_tentative_sys($username, $question, $tests, $tentative)
	{
		return $this->soumettre_tentative($username, $question, $tests, $tentative, new SoumettreTentativeSysInt());
	}

	private function soumettre_tentative($username, $question, $tests, $tentative, $intéracteur)
	{
		try {
			$résultat = $intéracteur->soumettre_tentative($username, $question, $tests, $tentative);
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

		if ($tentative == null) {
			throw new ContrôleurException("Requête intraitable.", 400);
		}
		return $résultat;
	}

	private function sauvegarder_tentative_et_avancement($username, $chemin, $question, $tentative)
	{
		$sauvegardeTentativeInt = new SauvegarderTentativeInt();
		$sauvegardeTentativeInt->sauvegarder($username, $chemin, $tentative);

		$avancementInt = new ObtenirAvancementInt();
		$avancement =
			$avancementInt->get_avancement($username, $chemin) ??
			new Avancement(tentatives: [], titre: $question->titre, niveau: $question->niveau);

		$avancement->ajouter_tentative($tentative);

		$avancementInt = new SauvegarderAvancementInt();
		$avancementInt->sauvegarder($username, $chemin, $avancement, $question);
	}

	private function récupérer_conteneur($username, $chemin)
	{
		$obtenirTentativeInt = new ObtenirTentativeInt();
		$tentative_récupérée = $obtenirTentativeInt->get_dernière($username, $chemin);
		return $tentative_récupérée ? $tentative_récupérée->conteneur : null;
	}
}
