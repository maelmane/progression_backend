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
use progression\domaine\interacteur\InscriptionInt;

class InscriptionCtl extends Contrôleur
{
	public function inscription(Request $request)
	{
		Log::debug("InscriptionCtl.inscription. Params : ", $request->all());
		Log::info("{$request->ip()} - Tentative d'inscription : {$request->input("username")}");

		$auth_local = getenv("AUTH_LOCAL") !== "false";
		$auth_ldap = getenv("AUTH_LDAP") === "true";

		if (!$auth_local && $auth_ldap) {
			return $this->réponse_json(["erreur" => "Inscription locale non supportée."], 403);
		}

		$erreurs = $this->valider_paramètres($request);
		if ($erreurs) {
			$réponse = $this->réponse_json(["erreur" => $erreurs], 400);
		} else {
			$réponse = $this->effectuer_inscription($request);
		}

		Log::debug("InscriptionCtl.inscription. Retour : ", [$réponse]);
		return $réponse;
	}

	private function effectuer_inscription($request)
	{
		Log::debug("InscriptionCtl.effectuer_inscription. Params : ", [$request]);

		$username = $request->input("username");
		$password = $request->input("password");

		$inscriptionInt = new InscriptionInt();
		$user = $inscriptionInt->effectuer_inscription($username, $password);

		$réponse = $this->valider_et_préparer_réponse($user, $request);

		Log::debug("InscriptionCtl.effectuer_inscription. Retour : ", [$réponse]);
		return $réponse;
	}

	private function valider_et_préparer_réponse($user, $request)
	{
		Log::debug("InscriptionCtl.valider_et_préparer_réponse. Params : ", [$user]);

		if ($user) {
			Log::info(
				"({$request->ip()}) - {$request->method()} {$request->path()} (" .
					get_class($this) .
					") Inscription. username: " .
					$request->input("username"),
			);

			$ressources = '{
				"ressources": [
				  {
					"url": "user/*",
					"method": "*"
				  }
				]
			  }';
			$expirationToken = time() + $_ENV["JWT_TTL"];
			$token = GénérateurDeToken::get_instance()->générer_token($user, $ressources, $expirationToken);
			$réponse = $this->préparer_réponse(["Token" => $token]);
		} else {
			Log::notice(
				"({$request->ip()}) - {$request->method()} {$request->path()} (" .
					get_class($this) .
					") Échec de l'inscription. username: " .
					$request->input("username"),
			);

			$réponse = $this->réponse_json(["erreur" => "Échec de l'inscription."], 403);
		}

		Log::debug("InscriptionCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);
		return $réponse;
	}

	private function valider_paramètres($request)
	{
		Log::debug("InscriptionCtl.valider_paramètres : ", $request->all());

		$validateur = Validator::make(
			$request->all(),
			[
				"username" => "required|alpha_dash",
			],
			[
				"required" => "Le champ :attribute est obligatoire.",
			],
		)->sometimes("password", "required", function ($input) {
			return getenv("AUTH_LOCAL") === "true";
		});

		if ($validateur->fails()) {
			$réponse = $validateur->errors();
		} else {
			$réponse = null;
		}

		Log::debug("InscriptionCtl.valider_paramètres. Retour : ", [$réponse]);
		return $réponse;
	}
}
