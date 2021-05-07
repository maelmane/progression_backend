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
use progression\domaine\interacteur\ObtenirSauvegardeAutomatiqueInt;
use progression\domaine\interacteur\CréerSauvegardeAutomatiqueInt;
use progression\http\transformer\SauvegardeAutomatiqueTransformer;
use progression\util\Encodage;
use progression\domaine\entité\Sauvegarde;

class SauvegardeCtl extends Contrôleur
{
	public function get(Request $request, $username, $question_uri, $langage)
	{
		$chemin = Encodage::base64_decode_url($question_uri);
		$sauvegarde = null;
		$réponse = null;
		$sauvegardeInt = new ObtenirSauvegardeAutomatiqueInt();
		$sauvegarde = $sauvegardeInt->get_sauvegarde_automatique($username, $chemin, $langage);

		if ($sauvegarde != null) {
			$sauvegarde->id = "{$username}/{$question_uri}/{$langage}";
			$réponse = $this->item($sauvegarde, new SauvegardeAutomatiqueTransformer());
		}

		return $this->préparer_réponse($réponse);
	}

	public function post(Request $request, $username, $question_uri)
	{
		$réponse = null;

		$chemin = Encodage::base64_decode_url($question_uri);
		$validation = $this->validationSauvegarde($request);
		if ($validation->fails()) {
			return $this->réponse_json(["erreur" => $validation->errors()], 422);
		}
		$sauvegarde = new Sauvegarde(
			(new \DateTime())->getTimestamp(),
			$request->code
		);
		$sauvegardeInt = new CréerSauvegardeAutomatiqueInt();

		$résultat_sauvegarde = $sauvegardeInt->sauvegarder($sauvegarde, $username, $chemin, $request->langage);

		if ($résultat_sauvegarde != null) {
			$résultat_sauvegarde->id = "{$username}/{$question_uri}/{$request->langage}";
			$réponse = $this->item($résultat_sauvegarde, new SauvegardeAutomatiqueTransformer());
		} else {
			return $this->réponse_json(["erreur" => "Requête intraitable"], 422);
		}
		return $this->préparer_réponse($réponse);
	}

	public function validationSauvegarde($request)
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
