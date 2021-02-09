<?php

namespace progression\providers;

use DomainException;
use Exception;
use Illuminate\Support\ServiceProvider;
use \Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use progression\dao\DAOFactory;
use progression\domaine\interacteur\CréerUserInt;
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
            $parties_token = explode(" ", $request->header('Authorization'));
            if (count($parties_token) == 2 && strtolower($parties_token[0])=="bearer" ) {
                $token = $parties_token[1];
            
                try {
                    $tokenDécodé = JWT::decode($token, env('JWT_SECRET'), array('HS256'));
                    // Compare le Unix Timestamp courant et l'expiration du token. 
                    if (time() > $tokenDécodé->expired) {
                        return null;
                    } else {
                        // Recherche de l'utilisateur
                        $user = (new CréerUserInt(new DAOFactory()))->obtenir_ou_créer_user(($tokenDécodé->user)->username);
                        $request->request->add(['username' => $user->username]);
                        
                        return $user;
                    }
                } catch (UnexpectedValueException | SignatureInvalidException | DomainException $e) {
                    error_log("Erreur de décodage: ".$e);
                    return null;
                }
            }
            else {
                return null;
            }
        });
    }
}
