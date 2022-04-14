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
		Log::info("(" . $request->ip() . ") - " . $request->method() . " " . $request->path() . "(" . __CLASS__ . ")");
		return $next($request);
	}
}
