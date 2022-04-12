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
use Illuminate\Auth\GenericUser;
use progression\domaine\interacteur\ObtenirUserInt;
use progression\domaine\entité\User;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use UnexpectedValueException;
use DomainException;
use progression\util\RessourceHelper;


class AuthServiceProvider extends ServiceProvider
{
	public function register()
	{
	}

	public function boot()
	{
		Gate::guessPolicyNamesUsing(function ($modelClass) {
			if ($modelClass == "progression\domaine\entité\User") {
				return "access-user";
			}
		});

		Gate::before(function ($user, $ability) {
			if ($user->rôle == User::ROLE_ADMIN) {
				return true;
			}
		});

		Gate::define("acces-ressource", function ($user, $request) {
			$tokenDécodé = $this->obtenirTokenDécodé($request);

			if(!$tokenDécodé) {
				return false;
			} else {
				$ressourceHelper = new RessourceHelper($tokenDécodé);
				return $ressourceHelper->vérifierSiContientUrl($request->path()) && $ressourceHelper->vérifierSiContientMethod($request->method());
			}
		});

		Gate::define("access-user", [UserPolicy::class, "access"]);

		Gate::define("update-avancement", function ($user) {
			return false;
		});

		$this->app["auth"]->viaRequest("api", function ($request) {
			$tokenDécodé = $this->obtenirTokenDécodé($request);

			if ($tokenDécodé && (time() < $tokenDécodé->expired || $tokenDécodé->expired == 0)) {
				$user = (new ObtenirUserInt())->get_user($tokenDécodé->username);

				return new GenericUser([
					"username" => $user->username,
					"rôle" => $user->rôle,
				]);
			} else {
				return null;
			}
		});
	}

	private function obtenirTokenDécodé($request)
	{
		if ($request->header("Authorization")) {
			$token = trim(str_ireplace("bearer", "", $request->header("Authorization")));

			try {
				return JWT::decode($token, $_ENV["JWT_SECRET"], ["HS256"]);
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
		return null;
	}
}
