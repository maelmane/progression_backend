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

namespace progression\providers;

use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Validator as ValidatorImpl;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use progression\domaine\interacteur\{ObtenirUserInt, LoginInt};
use progression\domaine\entité\user\{User, État, Rôle};
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use UnexpectedValueException;
use DomainException;
use Carbon\Carbon;

class AuthServiceProvider extends ServiceProvider
{
	public function register()
	{
	}

	public function boot()
	{
		Gate::guessPolicyNamesUsing(function ($modelClass) {
			if ($modelClass == "progression\domaine\entité\user\User") {
				return "acces-utilisateur";
			}
		});

		Gate::before(function ($user, $ability) {
			if ($user->rôle == Rôle::ADMIN) {
				return true;
			}
		});

		// À corriger?
		// Cause : "Cannot access offset 'auth' on Illuminate\Contracts\Foundation\Application."
		// auth est définit dans bootstrap/app.php
		// @phpstan-ignore-next-line
		$this->app["auth"]->viaRequest("api", function ($request) {
			// Fournit le User passé aux Gates à fins d'authentification et d'autorisation
			if (stripos($request->header("Authorization"), "bearer") === 0) {
				$tokenEncodé = trim(str_ireplace("bearer", "", $request->header("Authorization")));
				$tokenDécodé = $this->décoderToken($tokenEncodé, $request);
				if ($tokenDécodé && $this->vérifierExpirationToken($tokenDécodé)) {
					$obtenirUserInteracteur = new ObtenirUserInt();
					return $obtenirUserInteracteur->get_user($tokenDécodé["username"]);
				}
			}

			$identifiant = $request->input("identifiant");
			if ($identifiant) {
				$obtenirUserInteracteur = new ObtenirUserInt();
				return $obtenirUserInteracteur->get_user($identifiant) ??
					new User($identifiant, Carbon::now()->getTimestamp());
			}

			return null;
		});

		Gate::define("acces-utilisateur", function ($user, $request) {
			$token = trim(str_ireplace("bearer", "", $request->header("Authorization")));
			$tokenDécodé = $this->décoderToken($token, $request);
			if (
				is_array($tokenDécodé) &&
				array_key_exists("ressources", $tokenDécodé) &&
				is_array($tokenDécodé["ressources"]) &&
				$this->vérifierRessourceAutorisée($tokenDécodé["ressources"] ?: null, $request) &&
				mb_strtolower($user->username) == mb_strtolower($request->username)
			) {
				return true;
			}

			return false;
		});

		Gate::define("authentification_mdp", function ($user, $request) {
			// Valide l'authentification par mot de passe ou clé
			$validateur = $this->valider_paramètres($request);

			if ($validateur->fails()) {
				return Response::deny($validateur->errors());
			}

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
			if ($user == null) {
				return Response::deny();
			} else {
				return Response::allow();
			}
		});

		Gate::define("authentification_token", function ($user, $request) {
			// Authentification par token
			$tokenEncodé = trim(str_ireplace("bearer", "", $request->header("Authorization")));
			$tokenDécodé = $this->décoderToken($tokenEncodé, $request);
			if ($tokenDécodé && $this->vérifierExpirationToken($tokenDécodé)) {
				$obtenirUserInteracteur = new ObtenirUserInt();
				return $obtenirUserInteracteur->get_user($tokenDécodé["username"]) !== null;
			}
			return null;
		});

		Gate::define("acces-ressource", function ($user, $request) {
			$tokenRessource = $request->input("tkres");
			$tokenRessourceDécodé = $this->décoderToken($tokenRessource, $request);
			if (
				is_array($tokenRessourceDécodé) &&
				array_key_exists("ressources", $tokenRessourceDécodé) &&
				is_array($tokenRessourceDécodé["ressources"]) &&
				mb_strtolower($request->username) == mb_strtolower($tokenRessourceDécodé["username"]) &&
				$this->vérifierExpirationToken($tokenRessourceDécodé) &&
				$this->vérifierRessourceAutorisée($tokenRessourceDécodé["ressources"] ?: null, $request)
			) {
				return true;
			}

			return false;
		});

		Gate::define("utilisateur-non-inactif", function ($user, $request) {
			return $user->état != État::INACTIF;
		});

		Gate::define("utilisateur-validé", function ($user, $request) {
			return $user->état != État::ATTENTE_DE_VALIDATION;
		});

		Gate::define("soumettre-tentative", function ($user, $username) {
			return mb_strtolower($user->username) == mb_strtolower($username);
		});
	}

	private function décoderToken($tokenEncodé, $request)
	{
		try {
			//JWT::decode fournit une stdClass, le moyen le plus simple de transformer en array
			//est de réencoder/décoder en json.
			// @phpstan-ignore-next-line
			return json_decode(json_encode(JWT::decode($tokenEncodé, getenv("JWT_SECRET"), ["HS256"])), true);
		} catch (UnexpectedValueException | SignatureInvalidException | DomainException $e) {
			Log::notice(
				"(" .
					$request->ip() .
					") - " .
					$request->method() .
					" " .
					$request->path() .
					"(" .
					__CLASS__ .
					")" .
					" " .
					$e->getMessage(),
			);
			return null;
		}
	}

	private function vérifierExpirationToken($token)
	{
		return time() < $token["expired"] || $token["expired"] === 0;
	}

	private function vérifierRessourceAutorisée($ressources, $request)
	{
		if ($ressources) {
			foreach ($ressources as $ressource) {
				if (
					is_array($ressource) &&
					array_key_exists("url", $ressource) &&
					strlen($ressource["url"]) > 0 &&
					array_key_exists("method", $ressource) &&
					strlen($ressource["method"]) > 0 &&
					preg_match("#" . $ressource["url"] . "#", $request->path()) &&
					preg_match("#" . $ressource["method"] . "#i", $request->method())
				) {
					return true;
				}
			}
		}
		return false;
	}

	private function valider_paramètres(Request $request): ValidatorImpl
	{
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

		return $validateur;
	}
}
