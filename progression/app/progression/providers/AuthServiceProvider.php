<?php

namespace progression\providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\GenericUser;
use progression\dao\DAOFactory;
use progression\domaine\interacteur\ObtenirUserInt;
use progression\domaine\entité\User;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use UnexpectedValueException;
use DomainException;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Boot the authentication services for the application.
	 *
	 * @return void
	 */
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

		Gate::define("access-user", [UserPolicy::class, "access"]);

		Gate::define("update-avancement", function ($user) {
			return false;
		});

		// Décode le token de la requête.
		$this->app["auth"]->viaRequest("api", function ($request) {
			$parties_token = explode(" ", $request->header("Authorization"));
			if (count($parties_token) == 2 && strtolower($parties_token[0]) == "bearer") {
				$token = $parties_token[1];

				try {
					$tokenDécodé = JWT::decode($token, $_ENV["JWT_SECRET"], ["HS256"]);
					// Compare le Unix Timestamp courant et l'expiration du token.
					if ($tokenDécodé->expired!=null && time() > $tokenDécodé->expired) {
                        return null;
                    } else {
						// Recherche de l'utilisateur
						$user = (new ObtenirUserInt())->get_user($tokenDécodé->username);

						return new GenericUser([
							"username" => $user->username,
							"rôle" => $user->rôle,
						]);
					}
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
			} else {
				return null;
			}
		});
	}
}
