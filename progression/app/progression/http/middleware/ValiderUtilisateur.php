<?php

namespace progression\http\middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use progression\domaine\entité\User;
use progression\domaine\interacteur\InteracteurFactory;

class ValiderUtilisateur
{
	//protected $validerUtilisateur = true;

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @param  string|null  $guard
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$nomUtilisateur = $request->username;
		$utilisateurConnecté = $request->request->get("utilisateurConnecté");
		$intFactory = new InteracteurFactory();
		$utilisateurInt = $intFactory->getObtenirUserInt();

		if ($nomUtilisateur != null && $nomUtilisateur != "") {
			$utilisateurRecherché = $utilisateurInt->get_user($nomUtilisateur);

			switch ($utilisateurConnecté->rôle) {
				case User::ROLE_NORMAL:
					if ($utilisateurRecherché && $utilisateurConnecté->username == $utilisateurRecherché->username) {
						return $next($request);
					}
					break;
				case User::ROLE_ADMIN:
					return $next($request);
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
