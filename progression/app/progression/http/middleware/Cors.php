<?php
namespace progression\http\middleware;

use Closure;

class Cors
{
	public function handle($request, Closure $next)
	{
		return $next($request)
			->header("Access-Control-Allow-Origin", $_ENV["HTTP_ORIGIN"])
			->header("Access-Control-Allow-Methods", "PUT, POST, DELETE")
			->header("Access-Control-Allow-Headers", "Accept,Content-Type,Authorization,X-CSRF-TOKEN")
			->header("Access-Control-Allow-Credentials", "true");
	}
}
