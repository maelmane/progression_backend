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
use Illuminate\Http\JsonResponse;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use progression\domaine\interacteur\InscriptionInt;
use progression\domaine\entité\user\{User, État, Rôle};
use progression\http\transformer\UserTransformer;

class UserCréationCtl extends Contrôleur
{
	public function put(Request $request): JsonResponse
	{
		Log::debug("UserCréationCtl.inscription. Params : ", $request->all());
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

		Log::debug("UserCréationCtl.inscription. Retour : ", [$réponse]);
		return $réponse;
	}

	private function effectuer_inscription(Request $request): JsonResponse
	{
		Log::debug("UserCréationCtl.effectuer_inscription. Params : ", [$request]);

		$username = $request->input("username");
		$courriel = $request->input("courriel");
		$password = $request->input("password");

		$inscriptionInt = new InscriptionInt();
		$user = $inscriptionInt->effectuer_inscription($username, $courriel, $password);

		$réponse = $this->valider_et_préparer_réponse($user);

		Log::debug("UserCréationCtl.effectuer_inscription. Retour : ", [$réponse]);
		return $réponse;
	}

	private function valider_et_préparer_réponse(User|null $user): JsonResponse
	{
		Log::debug("UserCtl.valider_et_préparer_réponse. Params : ", [$user]);

		if ($user) {
			$user->id = $user->username;
			$réponse = $this->item($user, new UserTransformer());
		} else {
			$réponse = null;
		}

		$réponse = $this->préparer_réponse($réponse);

		Log::debug("UserCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);

		return $réponse;
	}

	private function valider_paramètres(Request $request): MessageBag|null
	{
		Log::debug("UserCréationCtl.valider_paramètres : ", $request->all());

		$validateur = Validator::make(
			$request->all(),
			[
				"username" => "required|regex:/^\w{1,64}$/u",
			],
			[
				"username.regex" =>
					"Err: 1003. Le nom d'utilisateur doit être composé de 2 à 64 caractères alphanumériques.",
				"username.unique" => "Err: 1001. Le nom d'utilisateur existe déjà.",
				"courriel.unique" => "Err: 1001. Le courriel existe déjà.",
				"courriel.email" => "Err: 1003. Le champ courriel doit être un courriel valide.",
				"password.regex" =>
					"Err: 1003. Le mot de passe doit contenir au moins 8 caractères, une majuscule et un chiffre.",
				"required" => "Err: 1004. Le champ :attribute est obligatoire.",
			],
		)
			->sometimes("courriel", "required|email|unique:progression\dao\models\UserMdl,courriel", function ($input) {
				return getenv("AUTH_LOCAL") === "true";
			})
			->sometimes("password", "required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/u", function ($input) {
				return getenv("AUTH_LOCAL") === "true";
			})
			->sometimes("username", "unique:progression\dao\models\UserMdl,username", function ($input) {
				return getenv("AUTH_LOCAL") === "true";
			});

		if ($validateur->fails()) {
			$réponse = $validateur->errors();
		} else {
			$réponse = null;
		}

		Log::debug("UserCréationCtl.valider_paramètres. Retour : ", [$réponse]);
		return $réponse;
	}
}
