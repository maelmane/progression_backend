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
			$obtenirUserInteracteur = new ObtenirUserInt();
			return $obtenirUserInteracteur->get_user($tokenDécodé->username);
		});

		Gate::define("acces-utilisateur", function ($user, $request) {
			$token = trim(str_ireplace("bearer", "", $request->header("Authorization")));
			$tokenDécodé = $this->décoderToken($token, $request);

			if (
				$tokenDécodé &&
				$this->vérifierExpirationToken($tokenDécodé) &&
				$this->vérifierRessourceAutorisée($tokenDécodé, $request) &&
				($user->username == $request->username || $request->username === null)
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
				$request->username == $tokenRessourceDécodé->username &&
				$this->vérifierExpirationToken($tokenRessourceDécodé) &&
				$this->vérifierRessourceAutorisée($tokenRessourceDécodé, $request)
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
		return time() < $token->expired || $token->expired === 0;
	}

	private function vérifierRessourceAutorisée($token, $request)
	{
		$autorisé = false;
		$ressourcesDécodées = json_decode($token->ressources, true);

		foreach ($ressourcesDécodées as $ressource) {
			$urlAutorisé = $ressource["url"];
			$méthodeAutorisée = $ressource["method"];

			$positionWildcard = strpos($urlAutorisé, "*");
			if ($positionWildcard === 0) {
				$autorisé = true;
			} elseif ($positionWildcard === false) {
				if ($request->path() == $urlAutorisé) {
					$autorisé = true;
				}
			} elseif ($positionWildcard === strlen($urlAutorisé) - 1) {
				$urlTronqué = substr($request->path(), 0, $positionWildcard - 1);
				if (substr($urlAutorisé, 0, $positionWildcard - 1) == $urlTronqué) {
					$autorisé = true;
				}
			} elseif ($positionWildcard < strlen($urlAutorisé) - 1) {
				$élémentsPathAutorisé = explode("/", $urlAutorisé);
				$élémentsPathDemandé = explode("/", $request->path());

				print_r($urlAutorisé);
				print_r($élémentsPathAutorisé);
				print_r($request->path());
				print_r($élémentsPathDemandé);

				if (count($élémentsPathAutorisé) !== count($élémentsPathDemandé)) {
					$autorisé = false;
				} else {
					$autorisé = true;
					for ($i = 0; $i < count($élémentsPathAutorisé); $i++) {
						if ($élémentsPathAutorisé[$i] !== "*") {
							if ($élémentsPathAutorisé[$i] !== $élémentsPathDemandé[$i]) {
								$autorisé = false;
							}
						}
					}
				}
			}

			if ($autorisé && $méthodeAutorisée != "*") {
				if ($méthodeAutorisée != $request->method()) {
					$autorisé = false;
				}
			}

			if ($autorisé) {
				return true;
			}
		}

		return false;
	}
}
