<?php

namespace progression\providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\GenericUser;
use Illuminate\Http\Request;
use progression\domaine\interacteur\ObtenirUserInt;
use progression\domaine\entité\User;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use UnexpectedValueException;
use DomainException;
use Exception;
use Vtiful\Kernel\Excel;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
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

		Gate::define("acces-ressource", function ($user, $request) {
			$estAutorisé = false;
			$tokenDécodé = $this->obtenirTokenDécodé($request);

			if ($tokenDécodé) {
				$jsonDécodé = json_decode($tokenDécodé->ressources, false);
				print_r($jsonDécodé);
				$urlAutorisé = $jsonDécodé->ressources->url;
				$méthodeAutorisée = $jsonDécodé->ressources->method;
				$positionWildcard = strpos($urlAutorisé, "*");

				if ($positionWildcard === false) {
					$estAutorisé = ($request->path() == $urlAutorisé) && ($request->method() == $méthodeAutorisée || $méthodeAutorisée == "*");
					print_r("Pas de wildcard");
				}
				elseif ($positionWildcard === 0) {
					$estAutorisé = ($request->method() == $méthodeAutorisée || $méthodeAutorisée == "*");
					print_r("Wildcard à la position 0");
				}
				else {
					$ressourceDemandée = substr($request->path(), 0, $positionWildcard);
					$ressourceAutorisée = substr($urlAutorisé, 0, $positionWildcard - 1);
					$estAutorisé = $ressourceDemandée == $ressourceAutorisée && ($request->method() == $méthodeAutorisée || $méthodeAutorisée == "*");
					print_r("Wildcard après la position 0");
				}
			}

			return $estAutorisé;
		});

		Gate::define("access-user", [UserPolicy::class, "access"]);

		Gate::define("update-avancement", function ($user) {
			return false;
		});

		$this->app["auth"]->viaRequest("api", function ($request) {
			$tokenDécodé = $this->obtenirTokenDécodé($request);

			if ($tokenDécodé && (time() < $tokenDécodé->expired || $tokenDécodé->expired == 0)) {
				$user = (new ObtenirUserInt())->get_user($tokenDécodé->username);
				print_r($tokenDécodé);

				return new GenericUser([
					"username" => $user->username,
					"rôle" => $user->rôle,
				]);
			}

			return null;
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
