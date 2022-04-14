<?php

namespace progression\http\middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Support\Facades\Log;

class Authenticate
{
	protected $auth;

	public function __construct(Auth $auth)
	{
		$this->auth = $auth;
	}

	//La sortie est récupérée par AuthServiceProvider.php, avec $this->app["auth"]->viaRequest("api", function ($request)
	public function handle($request, Closure $next, $guard = null)
	{
		if ($this->auth->guard($guard)->guest()) {
			Log::warning(
				"(" . $request->ip() . ") - " . $request->method() . " " . $request->path() . "(" . __CLASS__ . ")",
			);
			return response()->json(["erreur" => "Utilisateur non autorisé."], 401, [
				"Content-Type" => "application/json;charset=UTF-8",
				"Charset" => "utf-8",
			]);
		}
		Log::info("(" . $request->ip() . ") - " . $request->method() . " " . $request->path() . "(" . __CLASS__ . ")");
		return $next($request);
	}
}
