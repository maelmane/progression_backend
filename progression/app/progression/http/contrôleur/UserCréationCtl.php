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

		if ($auth_local) {
			$réponse = $this->effectuer_inscription_locale($request);
		} else {
			$réponse = $this->effectuer_inscription_non_locale($request);
		}

		Log::debug("UserCréationCtl.inscription. Retour : ", [$réponse]);
		return $réponse;
	}

	private function effectuer_inscription_non_locale(Request $request): JsonResponse
	{
		$auth_ldap = getenv("AUTH_LDAP") === "true";

		if ($auth_ldap) {
			$réponse = $this->réponse_json(["erreur" => "Inscription locale non supportée."], 403);
		} else {
			$erreurs = $this->valider_paramètres_sans_authentification($request);
			if ($erreurs) {
				$réponse = $this->réponse_json(["erreur" => $erreurs], 400);
			} else {
				$user = $this->effectuer_inscription_sans_mdp($request);
				$réponse = $this->valider_et_préparer_réponse($user);
			}
		}

		return $réponse;
	}

	private function effectuer_inscription_locale(Request $request): JsonResponse
	{
		$erreurs = $this->valider_paramètres_inscription_locale($request);

		if ($erreurs) {
			if (
				$erreurs->hasAny("username", "courriel") &&
				(str_starts_with($erreurs->first("username"), "Err: 1001.") ||
					str_starts_with($erreurs->first("courriel"), "Err: 1001."))
			) {
				$réponse = $this->réponse_json(["erreur" => $erreurs], 409);
			} else {
				$réponse = $this->réponse_json(["erreur" => $erreurs], 400);
			}
		} else {
			$user = $this->effectuer_inscription($request);
			if ($user === null) {
				$réponse = $this->réponse_json(["erreur" => "Opération interdite."], 403);
			} else {
				$réponse = $this->valider_et_préparer_réponse($user);
			}
		}

		return $réponse;
	}

	private function effectuer_inscription(Request $request): User|null
	{
		Log::debug("UserCréationCtl.effectuer_inscription. Params : ", [$request]);

		$username = $request->input("username");
		$courriel = $request->input("courriel");
		$password = $request->input("password");

		$inscriptionInt = new InscriptionInt();
		$user = $inscriptionInt->effectuer_inscription_locale($username, $courriel, $password);

		Log::debug("UserCréationCtl.effectuer_inscription. Retour : ", [$user]);

		return $user;
	}

	private function effectuer_inscription_sans_mdp(Request $request): User|null
	{
		Log::debug("UserCréationCtl.effectuer_inscription_sans_mdp. Params : ", [$request]);

		$username = $request->input("username");

		$inscriptionInt = new InscriptionInt();
		$user = $inscriptionInt->effectuer_inscription_sans_mdp($username);

		Log::debug("UserCréationCtl.effectuer_inscription_sans_mdp. Retour : ", [$user]);

		return $user;
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

	private function valider_paramètres_inscription_locale(Request $request): MessageBag|null
	{
		Log::debug("UserCréationCtl.valider_paramètres : ", $request->all());

		$réponse = $this->valider_paramètres_renvoi_courriel($request)
			? $this->valider_paramètres_nouvelle_inscritption($request)
			: null;

		Log::debug("UserCréationCtl.valider_paramètres. Retour : ", [$réponse]);
		return $réponse;
	}

	private function valider_paramètres_renvoi_courriel(Request $request): bool
	{
		Log::debug("UserCréationCtl.valider_paramètres_renvoi_courriel : ", $request->all());

		// Demande de retour de courriel de validation
		$validateur = Validator::make($request->all(), [
			"username" => "required|regex:/^\w{1,64}$/u|exists:progression\dao\models\UserMdl,username",
			"courriel" => "required|email|exists:progression\dao\models\UserMdl,courriel",
			"password" => "prohibited",
		]);

		$réponse = $validateur->fails() ? true : false;

		Log::debug("UserCréationCtl.valider_paramètres_renvoi_courriel. Retour : ", [$réponse]);

		return $réponse;
	}

	private function valider_paramètres_sans_authentification(Request $request): MessageBag|null
	{
		Log::debug("UserCréationCtl.valider_paramètres_sans_authentification : ", $request->all());

		$validateur = Validator::make(
			$request->all(),
			[
				"username" => "required|regex:/^\w{1,64}$/u",
				"courriel" => "prohibited",
				"password" => "prohibited",
			],
			[
				"username.regex" =>
					"Err: 1003. Le nom d'utilisateur doit être composé de 2 à 64 caractères alphanumériques.",
			],
		);

		if ($validateur->fails()) {
			$réponse = $validateur->errors();
		} else {
			$réponse = null;
		}

		Log::debug("UserCréationCtl.valider_paramètres_sans_authentification. Retour : ", [$réponse]);
		return $réponse;
	}

	private function valider_paramètres_nouvelle_inscritption(Request $request): MessageBag|null
	{
		Log::debug("UserCréationCtl.valider_paramètres_nouvelle_inscritption : ", $request->all());

		$validateur = Validator::make(
			$request->all(),
			[
				"username" => "required|regex:/^\w{1,64}$/u|unique:progression\dao\models\UserMdl,username",
				"courriel" => "required|email|unique:progression\dao\models\UserMdl,courriel",
				"password" => "required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/u",
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
		);

		if ($validateur->fails()) {
			$réponse = $validateur->errors();
		} else {
			$réponse = null;
		}

		Log::debug("UserCréationCtl.valider_paramètres_nouvelle_inscritption. Retour : ", [$réponse]);
		return $réponse;
	}
}
