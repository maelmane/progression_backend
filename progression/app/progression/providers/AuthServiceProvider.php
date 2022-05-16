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

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use progression\domaine\interacteur\ObtenirUserInt;
use progression\domaine\entité\User;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use UnexpectedValueException;
use DomainException;

class AuthServiceProvider extends ServiceProvider
{
	public function register()
	{
	}

	public function boot()
	{
		Gate::guessPolicyNamesUsing(function ($modelClass) {
			if ($modelClass == "progression\domaine\entité\User") {
				return "acces-utilisateur";
			}
		});

		Gate::before(function ($user, $ability) {
			if ($user->rôle == User::ROLE_ADMIN) {
				return true;
			}
		});

		$this->app["auth"]->viaRequest("api", function ($request) {
			$tokenEncodé = trim(str_ireplace("bearer", "", $request->header("Authorization")));
			$tokenDécodé = $this->décoderToken($tokenEncodé, $request);

			if ($tokenDécodé && $this->vérifierExpirationToken($tokenDécodé)) {
				$obtenirUserInteracteur = new ObtenirUserInt();
				return $obtenirUserInteracteur->get_user($tokenDécodé["username"]);
			}

			return null;
		});

		Gate::define("acces-utilisateur", function ($user, $request) {
			$token = trim(str_ireplace("bearer", "", $request->header("Authorization")));
			$tokenDécodé = $this->décoderToken($token, $request);
			if (
				$tokenDécodé &&
				$this->vérifierRessourceAutorisée($tokenDécodé["ressources"], $request) &&
				$user->username == $request->username
			) {
				return true;
			}

			return false;
		});

		Gate::define("acces-ressource", function ($user, $request) {
			$tokenRessource = $request->input("tkres");
			$tokenRessourceDécodé = $this->décoderToken($tokenRessource, $request);

			if (
				$tokenRessourceDécodé &&
				$request->username == $tokenRessourceDécodé["username"] &&
				$this->vérifierExpirationToken($tokenRessourceDécodé) &&
				$this->vérifierRessourceAutorisée($tokenRessourceDécodé["ressources"], $request)
			) {
				return true;
			}

			return false;
		});
	}

	private function décoderToken($tokenEncodé, $request)
	{
		try {
			return JWT::decode($tokenEncodé, $_ENV["JWT_SECRET"], ["HS256"]);
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
					strlen($ressource["url"]) > 0 &&
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
}
