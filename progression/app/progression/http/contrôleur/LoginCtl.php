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

use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use progression\domaine\interacteur\LoginInt;
use progression\domaine\entité\user\User;

class LoginCtl extends Contrôleur
{
	public function login(Request $request)
	{
		Log::debug("LoginCtl.login. Params : ", $request->all());
		Log::info("{$request->ip()} - Tentative de login : {$request->input("identifiant")}");

		$erreurs = $this->valider_paramètres($request);
		if ($erreurs) {
			$réponse = $this->réponse_json(["erreur" => $erreurs], 400);
		} else {
			$réponse = $this->effectuer_login($request);
		}

		Log::debug("LoginCtl.login. Retour : ", [$réponse]);
		return $réponse;
	}

	private function effectuer_login($request)
	{
		Log::debug("LoginCtl.effectuer_login. Params : ", [$request->all()]);

		$identifiant = $request->input("identifiant");
		$key_name = $request->input("key_name");
		$key_secret = $request->input("key_secret");
		$password = $request->input("password");
		$domaine = $request->input("domaine");

		$loginInt = new LoginInt();

		if ($key_name) {
			$user = $loginInt->effectuer_login_par_clé($identifiant, $key_name, $key_secret);
		} else {
			$user = $loginInt->effectuer_login_par_identifiant($identifiant, $password, $domaine);
		}

		if ($user && $this->valider_état_utilisateur($user, $request)) {
			$réponse = $this->valider_et_préparer_réponse($user, $request);
		} else {
			$réponse = $this->réponse_json(["erreur" => "Accès interdit."], 401);
		}

		Log::debug("LoginCtl.effectuer_login. Retour : ", [$réponse]);
		return $réponse;
	}

	private function valider_et_préparer_réponse($user, $request)
	{
		Log::debug("LoginCtl.valider_et_préparer_réponse. Params : ", [$user]);

		if ($user) {
			Log::info(
				"({$request->ip()}) - {$request->method()} {$request->path()} (" .
					get_class($this) .
					") Login. identifiant: " .
					$request->input("identifiant"),
			);

			$token = $this->générer_token($user);
			$réponse = $this->préparer_réponse(["Token" => $token]);
		} else {
			Log::notice(
				"({$request->ip()}) - {$request->method()} {$request->path()} (" .
					get_class($this) .
					") Accès interdit. identifiant: " .
					$request->input("identifiant"),
			);

			$réponse = $this->réponse_json(["erreur" => "Accès interdit."], 401);
		}

		Log::debug("LoginCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);
		return $réponse;
	}

	private function générer_token($user)
	{
		Log::debug("LoginCtl.générer_token. Params : ", [$user]);

		$expirationToken = time() + $_ENV["JWT_TTL"];
		$token = GénérateurDeToken::get_instance()->générer_token($user->username, $expirationToken);

		Log::debug("LoginCtl.générer_token. Retour : ", [$token]);
		return $token;
	}

	private function valider_paramètres($request)
	{
		Log::debug("LoginCtl.valider_paramètres : ", $request->all());

		$validateur = Validator::make(
			$request->all(),
			[
				"identifiant" => "required|string|between:2,64",
				"key_secret" => "required_with:key_name",
				"key_name" => "alpha_dash:ascii",
			],
			[
				"required" => "Err: 1004. Le champ :attribute est obligatoire.",
				"identifiant.regex" => "L'identifiant doit être un nom d'utilisateur ou un courriel valide.",
				"password.required_without" =>
					"Err: 1004. Le champ password est obligatoire lorsque key_name n'est pas présent.",
				"key_secret.required_with" =>
					"Err: 1004. Le champ key_secret est obligatoire lorsque key_name est présent.",
				"key_secret.required" => "Err: 1004. Le champ key_secret est obligatoire lorsque key_name est présent",
				"key_name.alpha_dash" => "Err: 1003. Le champ key_name doit être alphanumérique 'a-Z0-9-_'",
			],
		)
			->sometimes("password", "required_without:key_name", function ($input) {
				$auth_local = getenv("AUTH_LOCAL") !== "false";
				$auth_ldap = getenv("AUTH_LDAP") === "true";

				return $auth_local || $auth_ldap;
			})
			->sometimes("identifiant", "regex:/^\w{2,64}$/u", function ($input) {
				$auth_local = getenv("AUTH_LOCAL") !== "false";
				$auth_ldap = getenv("AUTH_LDAP") === "true";

				return isset($input->key_name) || (!$auth_local && !$auth_ldap);
			})
			->sometimes("identifiant", ["regex:/^\w{2,64}$|^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/u"], function ($input) {
				$auth_local = getenv("AUTH_LOCAL") !== "false";
				$auth_ldap = getenv("AUTH_LDAP") === "true";

				return !isset($input->key_name) && ($auth_local || $auth_ldap);
			});

		if ($validateur->fails()) {
			$réponse = $validateur->errors();
		} else {
			$réponse = null;
		}

		Log::debug("LoginCtl.valider_paramètres. Retour : ", [$réponse]);
		return $réponse;
	}

	private function valider_état_utilisateur(User $user, Request $request): bool
	{
		return Gate::forUser($user)->allows("utilisateur-validé", $request) &&
			Gate::forUser($user)->allows("utilisateur-non-inactif", $request);
	}
}
