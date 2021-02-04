<?php

namespace progression\providers;

use DomainException;
use Exception;
use Illuminate\Support\ServiceProvider;
use \Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use progression\domaine\interacteurs\LoginInteracteur;
use UnexpectedValueException;

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
        // Décode le token de la requête.
        $this->app['auth']->viaRequest('api', function ($request) {
            $token = $request->header('token');
            try {
                $tokenDécodé = JWT::decode($token, env('JWT_SECRET'), array('HS256'));
                // Compare le Unix Timestamp courant et l'expiration du token. 
                if (time() > $tokenDécodé->expired) {
                    return null;
                } else {
                    // Recherche de l'utilisateur
                    $LoginInt = new LoginInteracteur();
                    $user = $LoginInt->login(($tokenDécodé->user)->username);
                    return $user;
                }
            } catch (UnexpectedValueException | SignatureInvalidException | DomainException $e) {
                error_log("Erreur de décodage: ".$e);
                return null;
            }
        });
    }
}
