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
use progression\domaine\interacteur\{ObtenirAvancementInt, ObtenirAvancementsInt};
use progression\domaine\interacteur\SauvegarderAvancementInt;
use progression\domaine\interacteur\ObtenirQuestionInt;
use progression\http\transformer\AvancementTransformer;
use progression\util\Encodage;
use progression\domaine\entité\{User, Avancement, Question};

class AvancementCtl extends Contrôleur
{
	public function get(Request $request, $username, $question_uri = null)
	{
		Log::debug("AvancementCtl.get. Params : ", [$request->all(), $username, $question_uri]);

		if ($question_uri) {
			$avancements = $this->obtenir_avancement($username, $question_uri);
		} else {
			$avancements = $this->obtenir_avancements($username);
		}

		$réponse = $this->valider_et_préparer_réponse($avancements, $username, $question_uri);

		Log::debug("AvancementCtl.get. Retour : ", [$réponse]);
		return $réponse;
	}

	public function post(Request $request, $username)
	{
		Log::debug("AvancementCtl.post. Params : ", [$request->all(), $username]);

		$validateur = $this->valider_paramètres($request);

		if ($validateur->fails()) {
			$réponse = $this->réponse_json(["erreur" => $validateur->errors()], 400);
		} else {
			$avancement = $request->avancement;

			$avancement_sauvegardé = $this->créer_ou_sauvegarder_avancement(
				$avancement,
				$username,
				$request->question_uri,
			);

			$réponse = $this->valider_et_préparer_réponse($avancement_sauvegardé, $username, $request->question_uri);
		}

		Log::debug("AvancementCtl.post. Retour : ", [$réponse]);

		return $réponse;
	}

	private function valider_et_préparer_réponse($avancements, $username, $question_uri)
	{
		Log::debug("AvancementCtl.valider_et_préparer_réponse. Params : ", [$avancements, $username, $question_uri]);

		if ($avancements) {
			if (is_array($avancements)) {
				foreach ($avancements as $id => $avancement) {
					$avancement->id = $id;
				}
				$réponse = $this->collection($avancements, new AvancementTransformer());
			} else {
				$avancements->id = "{$username}/$question_uri";
				$réponse = $this->item($avancements, new AvancementTransformer());
			}
		} else {
			$réponse = null;
		}

		$réponse = $this->préparer_réponse($réponse);

		Log::debug("AvancementCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);
		return $réponse;
	}

	private function créer_ou_sauvegarder_avancement($avancement, $username, $question_uri)
	{
		Log::debug("AvancementCtl.créer_ou_sauvegarder_avancement. Params : ", [$avancement, $username, $question_uri]);

		$avancement_envoyé = $avancement;

		if ($avancement_envoyé == null) {
			$avancement_envoyé = $this->créer_avancement($question_uri);
		}

		$réponse = $this->sauvegarder_avancement($username, $question_uri, $avancement_envoyé);

		Log::debug("AvancementCtl.créer_ou_sauvegarder_avancement. Retour : ", [$réponse]);
		return $réponse;
	}

	private function valider_paramètres($request)
	{
		$validateur = Validator::make(
			$request->all(),
			[
				"question_uri" => "required",
				"avancement.état" => "required_with:avancement|integer|between:0,2",
			],
			[
				"required" => "Le champ :attribute est obligatoire.",
			],
		);

		return $validateur;
	}

	private function obtenir_avancement($username, $question_uri)
	{
		Log::debug("AvancementCtl.obtenir_avancement. Params : ", [$username, $question_uri]);

		$avancementInt = new ObtenirAvancementInt();

		$chemin = Encodage::base64_decode_url($question_uri);
		$avancement = $avancementInt->get_avancement($username, $chemin);

		Log::debug("AvancementCtl.obtenir_avancement. Retour : ", [$avancement]);
		return $avancement;
	}

	private function obtenir_avancements($username)
	{
		Log::debug("AvancementCtl.obtenir_avancements. Params : ", [$username]);

		$avancementInt = new ObtenirAvancementsInt();

		$avancements = $avancementInt->get_avancements($username);

		Log::debug("AvancementCtl.obtenir_avancements. Retour : ", [$avancements]);
		return $avancements;
	}

	private function sauvegarder_avancement($username, $question_uri, $avancement)
	{
		Log::debug("AvancementCtl.sauvegarder_avancement. Params : ", [$username, $question_uri]);

		$avancementInt = new SauvegarderAvancementInt();
		$chemin = Encodage::base64_decode_url($question_uri);

		$nouvel_avancement = $avancementInt->sauvegarder($username, $chemin, $avancement);
		$nouvel_avancement->id = "$username/$question_uri";

		Log::debug("AvancementCtl.sauvegarder_avancement. Retour : ", [$nouvel_avancement]);
		return $nouvel_avancement;
	}

	private function créer_avancement($question_uri)
	{
		Log::debug("AvancementCtl.créer_avancement. Params : ", [$question_uri]);

		$chemin = Encodage::base64_decode_url($question_uri);
		$question = (new ObtenirQuestionInt())->get_question($chemin);
		$avancement = new Avancement(
			QUESTION::ETAT_DEBUT,
			QUESTION::TYPE_PROG,
			[],
			$question->titre,
			$question->niveau,
		);

		Log::debug("AvancementCtl.créer_avancement. Retour : ", [$avancement]);
		return $avancement;
	}
}
