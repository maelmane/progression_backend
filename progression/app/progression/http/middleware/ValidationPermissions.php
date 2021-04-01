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
use progression\domaine\entité\User;
use progression\domaine\interacteur\InteracteurFactory;

class ValidationPermissions
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$nomUtilisateur = $request->username;
		$utilisateurConnecté = $request->request->get("utilisateurConnecté");
		$intFactory = new InteracteurFactory();
		$utilisateurInt = $intFactory->getObtenirUserInt();

		if ($nomUtilisateur) {
			$utilisateurRecherché = $utilisateurInt->get_user($nomUtilisateur);

			switch ($utilisateurConnecté->rôle) {
				case User::ROLE_NORMAL:
					if ($utilisateurRecherché && $utilisateurConnecté->username == $utilisateurRecherché->username) {
						return $next($request);
					}
					break;
				case User::ROLE_ADMIN:
					return $next($request);
					break;
			}
			return response()->json(
				["message" => "Accès interdit."],
				403,
				[
					"Content-Type" => "application/vnd.api+json",
					"Charset" => "utf-8",
				],
				JSON_UNESCAPED_UNICODE,
			);
		}
		return $next($request);
	}
}
