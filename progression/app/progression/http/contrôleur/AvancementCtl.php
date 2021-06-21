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
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use progression\domaine\interacteur\ObtenirAvancementInt;
use progression\domaine\interacteur\ObtenirUserInt;
use progression\domaine\interacteur\SauvegarderAvancementInt;
use progression\http\transformer\AvancementTransformer;
use progression\util\Encodage;
use progression\domaine\entité\{User, Avancement, Question};

class AvancementCtl extends Contrôleur
{
	public function get(Request $request, $username, $question_uri)
	{
		$chemin = Encodage::base64_decode_url($question_uri);
		$avancement = null;
		$réponse = null;

		$avancement = $this->obtenirAvancement($username, $chemin);
		if ($avancement != null) {
			$avancement->id = "{$username}/$question_uri";
			$réponse = $this->item($avancement, new AvancementTransformer());
		}

		return $this->préparer_réponse($réponse);
	}

	public function post(Request $request, $username)
	{
		$réponse = null;

		if (isset($request->question_uri)) {
			$chemin = Encodage::base64_decode_url($request->question_uri);

			if (isset($request->avancement)) {
				$avancement = $request->avancement;
				$validation = $this->validerParams($request);
				if ($validation->fails()) {
					return $this->réponse_json(["erreur" => $validation->errors()], 422);
				}

				if (Gate::denies("update-avancement")) {
					return $this->réponse_json(["erreur" => "Opération interdite."], 403);
				}

				$avancement_sauvegardé = $this->sauvegarderAvancement($username, $chemin, $avancement);
			} else {
				$avancement_sauvegardé = $this->sauvegarderAvancement($username, $chemin, new Avancement());
			}

			$avancement_sauvegardé->id = "{$username}/$request->question_uri";
			return $this->préparer_réponse($this->item($avancement_sauvegardé, new AvancementTransformer()));
		} else {
			return $this->réponse_json(["erreur" => "Requête intraitable."], 422);
		}
	}

	private function obtenirAvancement($username, $chemin)
	{
		$avancementInt = new ObtenirAvancementInt();
		$avancement = $avancementInt->get_avancement($username, $chemin);

		return $avancement;
	}

	private function sauvegarderAvancement($username, $chemin, $avancement)
	{
		$avancementInt = new SauvegarderAvancementInt();
		$new_avancement = $avancementInt->sauvegarder($username, $chemin, $avancement);
		return $new_avancement;
	}

	private function validerParams($request)
	{
		return Validator::make(
			$request->all(),
			[
				"avancement.état" => "required|integer|between:0,2",
			],
			[
				"required" => "Le champ :attribute est obligatoire.",
			],
		);
	}
}
