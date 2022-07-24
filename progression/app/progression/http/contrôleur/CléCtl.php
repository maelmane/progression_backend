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
use progression\domaine\entité\Clé;
use progression\domaine\interacteur\{ObtenirCléInt, GénérerCléAuthentificationInt};
use progression\http\transformer\CléTransformer;

class CléCtl extends Contrôleur
{
	public function get(Request $request, $username, $nom)
	{
		Log::debug("CléCtl.get. Params : ", [$request->all(), $username, $nom]);

		$nom_décodé = urldecode($nom);

		$clé = $this->obtenir_clé($username, $nom_décodé);
		$réponse = $this->valider_et_préparer_réponse($clé, $username, $nom_décodé);

		Log::debug("CléCtl.get. Retour : ", [$réponse]);
		return $réponse;
	}

	public function post(Request $request, $username)
	{
		Log::debug("CléCtl.post. Params : ", [$request->all(), $username]);

		$validateur = $this->valider_paramètres($request);

		if ($validateur->fails()) {
			$réponse = $this->réponse_json(["erreur" => $validateur->errors()], 400);
		} else {
			$cléInt = new GénérerCléAuthentificationInt();
			$clé = $cléInt->générer_clé($username, $request->nom, $request->expiration ?? 0);

			$réponse = $this->valider_et_préparer_réponse($clé, $username, $request->nom);
		}

		Log::debug("CléCtl.post. Retour : ", [$réponse]);
		return $réponse;
	}

	private function obtenir_clé($username, $nom)
	{
		Log::debug("CléCtl.obtenir_clé. Params : ", [$username, $nom]);

		$cléInt = new ObtenirCléInt();
		$réponse = $cléInt->get_clé($username, $nom);

		Log::debug("CléCtl.obtenir_clé. Retour : ", [$réponse]);
		return $réponse;
	}

	private function valider_et_préparer_réponse($clé, $username, $nom)
	{
		Log::debug("CléCtl.obtenir_clé. Params : ", [$clé, $username, $nom]);

		if ($clé) {
			$clé->id = $nom;
			$réponse_array = $this->item($clé, new CléTransformer($username));
		} else {
			$réponse_array = null;
		}

		$réponse = $this->préparer_réponse($réponse_array);

		Log::debug("CléCtl.obtenir_clé. Retour : ", [$réponse]);
		return $réponse;
	}

	private function valider_paramètres($request)
	{
		$validateur = Validator::make(
			$request->all(),
			[
				"nom" => "required",
				"expiration" => [
					"numeric",
					"integer",
					function ($attribute, $value, $fail) {
						if ($value > 0 && $value < time()) {
							$fail("Expiration invalide");
						}
					},
				],
			],
			[
				"required" => "Le champ :attribute est obligatoire.",
				"expiration.numeric" => "Expiration invalide",
				"expiration.integer" => "Expiration invalide",
			],
		);

		return $validateur;
	}
}
