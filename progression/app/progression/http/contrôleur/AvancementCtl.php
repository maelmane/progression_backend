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
		$avancement = null;
		$réponse = null;

		if (isset($request->question_uri)) {
			$chemin = Encodage::base64_decode_url($request->question_uri);

			if (isset($request->avancement)) {
				if ($request->user()->rôle == User::ROLE_ADMIN) {
					if (!isset($request->avancement["état"])) {
						return $this->réponse_json(
							["erreur" => "Le champ état est obligatoire pour enregistrer l'avancement."],
							422,
						);
					}

					$avancement = new Avancement([], $request->avancement["état"], Question::TYPE_PROG);
					$avancement = $this->sauvegarderAvancement($username, $chemin, $avancement);
				} else {
					return $this->réponse_json(["erreur" => "Accès interdit."], 403);
				}
			} else {
				$avancement = $this->obtenirAvancement($username, $chemin);
			}
			// On n'entrera ici que si l'utilisateur existe et <l'objet $avancement correspond bel et bien à un objet de la classe «Avancement»>(si applicable)
			if ($avancement != null) {
				$avancement->id = "{$username}/$request->question_uri";
				$réponse = $this->item($avancement, new AvancementTransformer());
			} else {
				return $this->réponse_json(["erreur" => "Requête intraitable"], 422);
			}
			return $this->préparer_réponse($réponse);
		} else {
			return $this->réponse_json(["erreur" => "Requête intraitable"], 422);
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
}
