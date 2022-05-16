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
use Illuminate\Support\Facades\Validator;
use progression\http\transformer\{TentativeProgTransformer, TentativeSysTransformer, TentativeBDTransformer};
use progression\domaine\interacteur\{
	ObtenirTentativeInt,
	ObtenirQuestionInt,
	SoumettreTentativeProgInt,
	SoumettreTentativeSysInt,
	SauvegarderAvancementInt,
	SauvegarderTentativeProgInt,
	SauvegarderTentativeSysInt,
};
use progression\domaine\entité\{TentativeProg, TentativeSys, TentativeBD};
use progression\domaine\entité\{QuestionProg, QuestionSys, QuestionBD};
use progression\domaine\entité\TestProg;
use progression\dao\exécuteur\ExécutionException;
use progression\util\Encodage;
use DomainException, LengthException, RuntimeException;

class TentativeCtl extends Contrôleur
{
	public function get(Request $request, $username, $question_uri, $timestamp)
	{
		$tentative = null;

		$chemin = Encodage::base64_decode_url($question_uri);

		$tentativeInt = new ObtenirTentativeInt();
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
		Log::debug("TentativeCtl.post. Params : ", [$request->all(), $username]);

		$TAILLE_CODE_MAX = (int) $_ENV["TAILLE_CODE_MAX"];
		$taille_code = mb_strlen($request->code);
		if ($taille_code > $TAILLE_CODE_MAX) {
			Log::error(
				"({$request->ip()}) - {$request->method()} {$request->path()} (" .
					__CLASS__ .
					") Le code soumis ${taille_code} > ${TAILLE_CODE_MAX} caractères.",
			);
			return $this->réponse_json(
				["erreur" => "Le code soumis ${taille_code} > ${TAILLE_CODE_MAX} caractères."],
				413,
			);
		}

		$tentative = null;
		$réponse = null;
		$chemin = Encodage::base64_decode_url($question_uri);

		$question = null;

		$questionInt = new ObtenirQuestionInt();
		try {
			$question = $questionInt->get_question($chemin);
		} catch (LengthException $erreur) {
			Log::error("({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ")");
			return $this->réponse_json(["erreur" => "Limite de volume dépassé."], 509);
		} catch (RuntimeException $erreur) {
			Log::error("({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ")");
			return $this->réponse_json(["erreur" => "Ressource indisponible sur le serveur distant."], 502);
		} catch (DomainException $erreur) {
			Log::notice(
				"({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ") Question inexistante",
			);
			return $this->réponse_json(["erreur" => "Requête intraitable."], 400);
		}

		if ($question instanceof QuestionProg) {
			$validation = $this->valider_paramètres($request);
			if ($validation->fails()) {
				Log::notice(
					"({$request->ip()}) - {$request->method()} {$request->path()} (" .
						__CLASS__ .
						") Paramètres invalides",
				);
				return $this->réponse_json(["erreur" => $validation->errors()], 400);
			}

			if (!empty($request->test)) {
				if (isset($request->test["entrée"]) || isset($request->test["params"])) {
					$question->tests = [
						new TestProg(
							$request->test["nom"] ?? "",
							$request->test["sortie_attendue"] ?? "",
							$request->test["entrée"] ?? "",
							$request->test["params"] ?? "",
						),
					];
				}
				Log::debug("TentativeCtl.post. Tests question : ", [$question->tests]);
			}
			$tentative = new TentativeProg($request->langage, $request->code, (new \DateTime())->getTimestamp());

			try {
				$tentativeInt = new SoumettreTentativeProgInt();
				$tentative = $tentativeInt->soumettre_tentative($username, $question, $tentative);
			} catch (ExécutionException $e) {
				Log::error($e->getMessage());
				if ($e->getPrevious()) {
					Log::error($e->getPrevious()->getMessage());
				}
				return $this->réponse_json(["erreur" => "Service non disponible."], 503);
			}
			if ($tentative == null) {
				Log::notice(
					"({$request->ip()}) - {$request->method()} {$request->path()} (" .
						__CLASS__ .
						") Requête intraitable (Tentative == null)",
				);
				return $this->réponse_json(["erreur" => "Requête intraitable."], 400);
			}

			try {
				if (empty($request->test)) {
					$avancementInt = new SauvegarderAvancementInt();
					$sauvegardeTentativeInt = new SauvegarderTentativeProgInt();
					$avancement = $avancementInt->récupérer_avancement($username, $question, $tentative);
					$avancement->titre = $question->titre;
					$avancement->niveau = $question->niveau;
					$avancementInt->mettre_à_jour_dates_et_état(
						$avancement,
						$tentative->date_soumission,
						$username,
						$question->uri,
					);
					$sauvegardeTentativeInt->sauvegarder($username, $question->uri, $tentative);
				}
			} catch (ExécutionException $e) {
				Log::error($e->getMessage());
				if ($e->getPrevious()) {
					Log::error($e->getPrevious()->getMessage());
				}
				return $this->réponse_json(["erreur" => "Service non disponible."], 503);
			}

			$tentative->id = "{$username}/{$question_uri}/{$tentative->date_soumission}";
			$réponse = $this->item($tentative, new TentativeProgTransformer());
		} elseif ($question instanceof QuestionSys) {
			$validation = $this->valider_paramètres_sys($request);
			if ($validation->fails()) {
				Log::notice(
					"({$request->ip()}) - {$request->method()} {$request->path()} (" .
						__CLASS__ .
						") Paramètres invalides",
				);
				return $this->réponse_json(["erreur" => $validation->errors()], 400);
			}

			$tentative = new TentativeSys($request->conteneur, $request->réponse, (new \DateTime())->getTimestamp());

			try {
				$tentativeInt = new SoumettreTentativeSysInt();
				$tentative = $tentativeInt->soumettre_tentative($username, $question, $tentative);
			} catch (ExécutionException $e) {
				Log::error($e->getMessage());
				if ($e->getPrevious()) {
					Log::error($e->getPrevious()->getMessage());
				}
				return $this->réponse_json(["erreur" => "Service non disponible."], 503);
			}
			if ($tentative == null) {
				Log::notice(
					"({$request->ip()}) - {$request->method()} {$request->path()} (" .
						__CLASS__ .
						") Requête intraitable (Tentative == null)",
				);
				return $this->réponse_json(["erreur" => "Requête intraitable."], 400);
			}

			try {
				if (empty($request->test)) {
					$avancementInt = new SauvegarderAvancementInt();
					$sauvegardeTentativeInt = new SauvegarderTentativeSysInt();
					$avancement = $avancementInt->récupérer_avancement($username, $question, $tentative);
					$avancement->titre = $question->titre;
					$avancement->niveau = $question->niveau;
					$avancementInt->mettre_à_jour_dates_et_état(
						$avancement,
						$tentative->date_soumission,
						$username,
						$question->uri,
					);
					$sauvegardeTentativeInt->sauvegarder($username, $question->uri, $tentative);
				}
			} catch (ExécutionException $e) {
				Log::error($e->getMessage());
				if ($e->getPrevious()) {
					Log::error($e->getPrevious()->getMessage());
				}
				return $this->réponse_json(["erreur" => "Service non disponible."], 503);
			}

			$tentative->id = "{$username}/{$question_uri}/{$tentative->date_soumission}";
			$réponse = $this->item($tentative, new TentativeSysTransformer());
		} elseif ($question instanceof QuestionBD) {
			Log::error("({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ")");
			return $this->réponse_json(["erreur" => "Question BD non implémentée."], 501);
		}

		Log::debug("TentativeCtl.post. Retour : ", $réponse);

		return $this->préparer_réponse($réponse);
	}

	private function valider_paramètres($request)
	{
		return Validator::make(
			$request->all(),
			[
				"langage" => "required",
				"code" => "required",
			],
			[
				"required" => "Le champ :attribute est obligatoire.",
			],
		);
	}

	private function valider_paramètres_sys($request)
	{
		return Validator::make(
			$request->all(),
			[
				"conteneur" => "required",
			],
			[
				"required" => "Le champ :attribute est obligatoire.",
			],
		);
	}
}
