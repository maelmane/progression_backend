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

		//La sortie représente l'utilisateur connecté, si existant dans la BD, et est fourni au framework
		$this->app["auth"]->viaRequest("api", function ($request) {
			$tokenEncodé = trim(str_ireplace("bearer", "", $request->header("Authorization")));
			$tokenDécodé = $this->décoderToken($tokenEncodé, $request);
			$obtenirUserInteracteur = new ObtenirUserInt();
			return $obtenirUserInteracteur->get_user($tokenDécodé->username);
		});

		//Le paramètre $user est fourni par le framework et est l'utilisateur connecté.
		Gate::define("acces-utilisateur", function ($user, $request) {
			$token = trim(str_ireplace("bearer", "", $request->header("Authorization")));
			$tokenDécodé = $this->décoderToken($token, $request);

			if (
				$tokenDécodé &&
				$this->vérifierExpirationToken($tokenDécodé) &&
				($user->username == $request->username || $request->username === null)
			) {
				return true;
			}

			return false;
		});

		Gate::define("acces-ressource", function ($user, $request) {
			$tokenRessource = $request->input("tkres");
			$tokenRégulier = trim(str_ireplace("bearer", "", $request->header("Authorization")));
			$tokenRessourceDécodé = $this->décoderToken($tokenRessource, $request);
			$tokenRégulierDécodé = $this->décoderToken($tokenRégulier, $request);

			if (
				$tokenRessourceDécodé &&
				$tokenRégulierDécodé &&
				$this->vérifierExpirationToken($tokenRessourceDécodé) &&
				$this->vérifierExpirationToken($tokenRessourceDécodé) &&
				$this->vérifierRessourceAutorisé($tokenRessourceDécodé, $request)
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
			Log::error(
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
					$e,
			);
			return null;
		}
	}

	private function vérifierExpirationToken($token)
	{
		return time() < $token->expired || $token->expired == 0;
	}

	private function vérifierRessourceAutorisé($token, $request)
	{
		$ressourcesDécodées = json_decode($token->ressources, false);
		return $this->vérifierPathAutorisé($request->path(), $ressourcesDécodées->ressources->url) &&
			$this->vérifierMethodAutorisé($request->method(), $ressourcesDécodées->ressources->method);
	}

	private function vérifierMethodAutorisé($methodDemandé, $methodAutorisé)
	{
		if ($methodDemandé == $methodAutorisé || $methodAutorisé == "*") {
			return true;
		}
		return false;
	}

	private function vérifierPathAutorisé($urlDemandé, $urlAutorisé)
	{
		foreach ($urlAutorisé as $url) {
			$positionWildcard = strpos($url, "*");
			if ($positionWildcard === 0) {
				return true;
			} elseif ($positionWildcard === false) {
				if ($urlDemandé == $url) {
					return true;
				}
			} else {
				$urlTronqué = substr($urlDemandé, 0, $positionWildcard - 1);
				if (substr($url, 0, $positionWildcard - 1) == $urlTronqué) {
					return true;
				}
			}
		}
		return false;
	}
}
