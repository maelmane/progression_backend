<?php

namespace progression\http\middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Support\Facades\Log;


class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->guest()) {
            Log::info("Le token est invalide pour: " . $request->ip() . " (AuthServiceProvider)");
            return response()->json(['message' => 'Utilisateur non autorisé.'], 401, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8']);
        }
        Log::info("Le token est valide pour: " . $request->ip() . " (AuthServiceProvider)");
        return $next($request);
    }
}
