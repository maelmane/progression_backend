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

   .	You should have received a copy of the GNU General Public License
   along with Progression.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace progression\http\contrôleur;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use progression\domaine\interacteur\ObtenirSauvegardeInt;
use progression\domaine\interacteur\EnregistrerSauvegardeInt;
use progression\http\transformer\SauvegardeTransformer;
use progression\util\Encodage;
use progression\domaine\entité\Sauvegarde;

class SauvegardeCtl extends Contrôleur
{
	public function get(Request $request, $username, $question_uri, $langage)
	{
		Log::debug("SauvegardeCtl.get. Params : ", [$request->all(), $username, $question_uri, $langage]);

		$sauvegarde = $this->obtenir_sauvegarde($username, $question_uri, $langage);
		$réponse = $this->valider_et_préparer_réponse($sauvegarde, $username, $question_uri, $langage);

		Log::debug("SauvegardeCtl.get. Retour : ", [$réponse]);
		return $réponse;
	}

	public function post(Request $request, $username, $question_uri)
	{
		Log::debug("SauvegardeCtl.post. Params : ", [$request->all(), $username, $question_uri]);

		$réponse = null;

		$validateur = $this->valider_paramètres($request);
		if ($validateur->fails()) {
			$réponse = $this->réponse_json(["erreur" => $validateur->errors()], 400);
		} else {
			$résultat_sauvegarde = $this->sauvegarder_sauvegarde($request, $username, $question_uri);
			$réponse = $this->valider_et_préparer_réponse(
				$résultat_sauvegarde,
				$username,
				$question_uri,
				$request->langage,
			);
		}

		Log::debug("SauvegardeCtl.post. Retour : ", [$réponse]);
		return $réponse;
	}

	private function obtenir_sauvegarde($username, $question_uri, $langage)
	{
		Log::debug("SauvegardeCtl.obtenir_sauvegarde. Params : ", [$username, $question_uri, $langage]);

		$chemin = Encodage::base64_decode_url($question_uri);

		$sauvegardeInt = new ObtenirSauvegardeInt();
		$sauvegarde = $sauvegardeInt->get_sauvegarde($username, $chemin, $langage, $this->get_includes());

		Log::debug("SauvegardeCtl.obtenir_sauvegarde. Retour : ", [$sauvegarde]);
		return $sauvegarde;
	}

	private function valider_et_préparer_réponse($sauvegarde, $username, $question_uri, $langage)
	{
		Log::debug("SauvegardeCtl.valider_et_préparer_réponse. Params : ", [$username, $question_uri, $langage]);

		$sauvegarde_array = null;

		if ($sauvegarde != null) {
			$sauvegarde->id = $langage;
			$sauvegarde_array = $this->item($sauvegarde, new SauvegardeTransformer("$username/$question_uri"));
		}

		$réponse = $this->préparer_réponse($sauvegarde_array);

		Log::debug("SauvegardeCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);
		return $réponse;
	}

	private function sauvegarder_sauvegarde($request, $username, $question_uri)
	{
		Log::debug("SauvegardeCtl.sauvegarder_sauvegarde. Params : ", [$request->all(), $username, $question_uri]);

		$sauvegarde = new Sauvegarde((new \DateTime())->getTimestamp(), $request->code);
		$sauvegardeInt = new EnregistrerSauvegardeInt();

		$chemin = Encodage::base64_decode_url($question_uri);
		$résultat_sauvegarde = $sauvegardeInt->enregistrer($username, $chemin, $request->langage, $sauvegarde);

		Log::debug("SauvegardeCtl.sauvegarder_sauvegarde. Retour : ", [$résultat_sauvegarde]);
		return $résultat_sauvegarde;
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
				"required" => "Err: 1004. Le champ :attribute est obligatoire.",
			],
		);
	}
}
