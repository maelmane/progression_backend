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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class Authenticate
{
	protected $auth;

	public function __construct(Auth $auth)
	{
		$this->auth = $auth;
	}

	public function handle(Request $request, Closure $next): JsonResponse
	{
		if (
			$request->header("Authorization") !== null &&
			is_string($request->header("Authorization")) &&
			stripos($request->header("Authorization"), "bearer") === 0
		) {
			return $this->authentifier_par_token($request, $next);
		} else {
			return $this->authentifier_par_mdp($request, $next);
		}
	}

	private function authentifier_par_token(Request $request, Closure $next): JsonResponse
	{
		if (Gate::allows("authentification_token", $request)) {
			return $next($request);
		} else {
			Log::warning(
				"(" . $request->ip() . ") - " . $request->method() . " " . $request->path() . "(" . __CLASS__ . ")",
			);
			return response()->json(["erreur" => "Accès interdit."], 401, [
				"Content-Type" => "application/vnd.api+json",
				"Charset" => "utf-8",
			]);
		}
	}

	private function authentifier_par_mdp(Request $request, Closure $next): JsonResponse
	{
		$réponse = Gate::inspect("authentification_mdp", $request);
		if ($réponse->allowed()) {
			return $next($request);
		} else {
			if ($réponse->message() == null) {
				return response()->json(["erreur" => "Accès interdit."], 401, [
					"Content-Type" => "application/vnd.api+json",
					"Charset" => "utf-8",
				]);
			}
			return response()->json(["erreur" => $réponse->message()], 400, [
				"Content-Type" => "application/vnd.api+json",
				"Charset" => "utf-8",
			]);
		}
	}
}
