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
use Illuminate\Support\Facades\Validator;
use progression\domaine\interacteur\LoginInt;

class LoginCtl extends Contrôleur
{
	public function login(Request $request)
	{
		Log::debug("LoginCtl.login. Params : ", $request->all());
		Log::info("{$request->ip()} - Tentative de login : {$request->input("username")}");

		$user = null;
		$token = null;

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
		Log::debug("LoginCtl.effectuer_login. Params : ", [$request]);

		$username = $request->input("username");
		$key_name = $request->input("key_name");
		$key_secret = $request->input("key_secret");
		$password = $request->input("password");
		$domaine = $request->input("domaine");

		$loginInt = new LoginInt();

		if ($key_name && $key_secret) {
			$user = $loginInt->effectuer_login_par_clé($username, $key_name, $key_secret);
		} else {
			$user = $loginInt->effectuer_login_par_identifiant($username, $password, $domaine);
		}

		$réponse = $this->valider_et_préparer_réponse($user, $request);

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
					") Login. username: " .
					$request->input("username"),
			);

			$token = $this->générer_token($user);
			$réponse = $this->préparer_réponse(["Token" => $token]);
		} else {
			Log::notice(
				"({$request->ip()}) - {$request->method()} {$request->path()} (" .
					get_class($this) .
					") Accès interdit. username: " .
					$request->input("username"),
			);

			$réponse = $this->réponse_json(["erreur" => "Accès interdit."], 401);
		}

		Log::debug("LoginCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);
		return $réponse;
	}

	private function générer_token($user)
	{
		Log::debug("LoginCtl.générer_token. Params : ", [$user]);

		$ressources = '{
			"ressources": {
			  "url": "*",
			  "method": "GET"
			}
		  }';
		$expirationToken = time() + $_ENV["JWT_TTL"];
		$token = GénérateurDeToken::get_instance()->générer_token($user->username, $ressources, $expirationToken);

		Log::debug("LoginCtl.générer_token. Retour : ", [$token]);
		return $token;
	}

	private function valider_paramètres($request)
	{
		Log::debug("LoginCtl.valider_paramètres : ", $request->all());

		$validateur = Validator::make(
			$request->all(),
			[
				"username" => "required|alpha_dash",
				"key_secret" => "required_with:key_name",
			],
			[
				"required" => "Le champ :attribute est obligatoire.",
			],
		)->sometimes("password", "required_without:key_name", function ($input) {
			$auth_local = getenv("AUTH_LOCAL") !== "false";
			$auth_ldap = getenv("AUTH_LDAP") === "true";

			return $auth_local || $auth_ldap;
		});

		if ($validateur->fails()) {
			$réponse = $validateur->errors();
		} else {
			$réponse = null;
		}

		Log::debug("LoginCtl.valider_paramètres. Retour : ", [$réponse]);
		return $réponse;
	}
}
