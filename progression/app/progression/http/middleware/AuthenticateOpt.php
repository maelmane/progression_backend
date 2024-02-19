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
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AuthenticateOpt
{
	public function handle(Request $request, Closure $next): JsonResponse
	{
		$user = $request->user("api");
		if ($user === false) {
			// Pas d'authentification fournie, ni nécessaire
			return $next($request);
		}

		if (!Gate::allows("utilisateur-non-inactif", $request)) {
			// Authentification fournie, mais utilisateur non authentifié
			return $this->réponse_json(["erreur" => "Accès interdit."], 401);
		}

		// Utilisateur authentifié
		return $next($request);
	}

	private function réponse_json(mixed $réponse, int $code): JsonResponse
	{
		return response()->json(
			$réponse,
			$code,
			[
				"Content-Type" => "application/vnd.api+json",
				"Charset" => "utf-8",
			],
			JSON_UNESCAPED_UNICODE,
		);
	}
}
