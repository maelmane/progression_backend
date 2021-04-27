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
use progression\domaine\interacteur\ObtenirAvancementInt;
use progression\domaine\interacteur\ObtenirUserInt;
use progression\domaine\interacteur\SauvegarderAvancementInt;
use progression\http\transformer\AvancementTransformer;
use progression\util\Encodage;
use progression\domaine\entité\User;

class AvancementCtl extends Contrôleur
{
	public function get(Request $request, $username, $question_uri)
	{
		$chemin = Encodage::base64_decode_url($question_uri);
		$avancement = null;
		$avancementInt = new ObtenirAvancementInt();
		$avancement = $avancementInt->get_avancement($username, $chemin);

		$réponse = null;

		if ($avancement != null) {
			$avancement->id = "{$username}/$question_uri";

			$réponse = $this->item($avancement, new AvancementTransformer());
		}

		return $this->préparer_réponse($réponse);
	}

	public function post(Request $request, $username, $question_uri)
	{
		$avancement = null;
		$réponse = null;
		$chemin = Encodage::base64_decode_url($question_uri);

		if(isset($request->avancement)){
			$validation = $this->validationAvancement($request->avancement);
			if ($validation->fails()) {
				return $this->réponse_json(["erreur" => $validation->errors()], 422);
			}
			$avancementReq = json_decode($request->avancement);
			if($avancementReq != null){
				$avancementInt = new SauvegarderAvancementInt();
				$avancement = $avancementInt->sauvegarder($username, $chemin, $avancementReq);
			} else{
				return $this->réponse_json(["erreur" => "Le format de l'avancement est intraitable."], 422);
			}
		} else{
			$avancementInt = new ObtenirAvancementInt();
			$avancement = $avancementInt->get_avancement($username, $chemin);
		}
		// On n'entrera ici que si l'utilisateur existe et <l'objet $avancement correspond bel et bien à un objet de la classe «Avancement»>(si applicable)
		if($avancement != null){
			$avancement->id = "{$username}/$question_uri";
			$réponse = $this->item($avancement, new AvancementTransformer());
		}else{
			return $this->réponse_json(["erreur" => "Requête incorrecte !!!"], 422);
		}
		return $this->préparer_réponse($réponse);
	}

	public function validationAvancement($request)
	{
		return Validator::make(
			$request->all(),
			[
				"état" => "required"
			],
			[
				"required" => "Le champ :attribute est obligatoire pour enregistrer l'avancement.",
			]
		);
	}
}
