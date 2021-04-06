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
	public function handle($request, Closure $next)
	{
		$nomUtilisateur = $request->username;
		$utilisateurConnecté = $request->request->get("utilisateurConnecté");

		$intFactory = new InteracteurFactory();
		$utilisateurInt = $intFactory->getObtenirUserInt();

		if (!$nomUtilisateur) {
			$utilisateurRecherché = $utilisateurConnecté;
		} else {
			$utilisateurRecherché = $utilisateurInt->get_user($nomUtilisateur);
		}

		$réponse = response()->json(
			["erreur" => "Accès interdit."],
			403,
			[
				"Content-Type" => "application/vnd.api+json",
				"Charset" => "utf-8",
			],
			JSON_UNESCAPED_UNICODE
		);

		if ($utilisateurRecherché && $utilisateurConnecté) {
			switch ($utilisateurConnecté->rôle) {
				case User::ROLE_NORMAL:
					if (
						$utilisateurConnecté->username ==
						$utilisateurRecherché->username
					) {
						$réponse = $next($request);
					}
					break;
				case User::ROLE_ADMIN:
					$réponse = $next($request);
					break;
			}
		}

		return $réponse;
	}
}
