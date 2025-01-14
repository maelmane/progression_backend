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
use progression\domaine\interacteur\{ObtenirSauvegardeInt, EnregistrerSauvegardeInt, IntéracteurException};
use progression\http\transformer\SauvegardeTransformer;
use progression\http\transformer\dto\GénériqueDTO;
use progression\util\Encodage;
use progression\domaine\entité\Sauvegarde;

class SauvegardeCtl extends Contrôleur
{
	public function get(Request $request, $username, $question_uri, $langage)
	{
		Log::debug("SauvegardeCtl.get. Params : ", [$request->all(), $username, $question_uri, $langage]);

		$réponse = null;

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
			$id = array_key_first($résultat_sauvegarde);
			$réponse = $this->valider_et_préparer_réponse($résultat_sauvegarde[$id], $username, $question_uri, $id);
		}

		Log::debug("SauvegardeCtl.post. Retour : ", [$réponse]);
		return $réponse;
	}

	/**
	 * @return array<string>
	 */
	public static function get_liens(string $id, string $langage): array
	{
		$urlBase = Contrôleur::$urlBase;

		return [
			"self" => "{$urlBase}/sauvegarde/{$id}/{$langage}",
			"avancement" => "{$urlBase}/avancement/{$id}",
		];
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

		if ($sauvegarde != null) {
			$dto = new GénériqueDTO(
				id: "$username/$question_uri/$langage",
				objet: $sauvegarde,
				liens: SauvegardeCtl::get_liens("$username/$question_uri", $langage),
			);

			$réponse = $this->item($dto, new SauvegardeTransformer());
		} else {
			$réponse = null;
		}

		$réponse = $this->préparer_réponse($réponse);
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
				"required" => "Le champ :attribute est obligatoire.",
			],
		);
	}
}
