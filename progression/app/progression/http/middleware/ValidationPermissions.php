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

namespace progression\http\middleware;

use Closure;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use progression\domaine\interacteur\ObtenirUserInt;
use Exception;

class ValidationPermissions
{
	public function handle($request, Closure $next)
	{
		$utilisateurRequête = $request->username;
		$utilisateurConnecté = $request->user();

		$utilisateurRecherché = (new ObtenirUserInt())->get_user($utilisateurRequête ?? $utilisateurConnecté->username);

		if (
			$utilisateurRecherché &&
			Gate::allows("access-user", $utilisateurRecherché) 
		) {
			return $next($request);
		} else {
			return response()->json(
				["erreur" => "Opération interdite."],
				403,
				[
					"ContentType" => "application/vnd.api+json",
					"Charset" => "utf8",
				],
				JSON_UNESCAPED_UNICODE,
			);
		}
	}

	private function validerRessourceDemandée($request)
	{
		$token = trim(str_ireplace("bearer", "", $request->header("Authorization")));

		if ($token) {
			try {
				$tokenDécodé = JWT::decode($token, $_ENV["JWT_SECRET"], ["HS256"]);
			} catch (Exception $e) {
				Log::error($e); //TODO: logger complètement l'erreur.
				return false;
			}

			if ($tokenDécodé->ressources == "*") {
				return true;
			}

			$path = $request->path();
			$ressourceDemandéePath = substr($path, 0, strpos($path, "/"));
			$ressourceDemandéeToken = substr($tokenDécodé->ressources, 0, strpos($tokenDécodé->ressources, "/"));

			if ($ressourceDemandéeToken == $ressourceDemandéePath) {
				return true;
			}
		}

		return false;
	}
}
