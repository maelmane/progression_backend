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
use Laravel\Lumen\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;

class PermissionsRessources
{
	public function handle(Request $request, Closure $next): JsonResponse
	{
		if (
			Gate::allows("acces-utilisateur", $request) ||
			($request->input("tkres") && Gate::allows("acces-ressource", $request))
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
}
