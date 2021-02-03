<?php

namespace progression\providers;

use DateTime;
use Exception;
use Illuminate\Support\ServiceProvider;
use \Firebase\JWT\JWT;
use progression\dao\mock\DAOFactory;

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
        // décode le token
        $this->app['auth']->viaRequest('api', function ($request) {
            $token = $request->header('token');
            try {
                $tokenDécodé = JWT::decode($token, env('JWT_SECRET'), array('HS256'));
                $différence = (date_diff(new DateTime($tokenDécodé->date), (new DateTime("now"))))->format("%a");
                if ($différence >= 1) {
                    return null;
                } else {
                    $factory = new DAOFactory();
                    $userDAO = $factory->get_user_dao();
                    $user = $userDAO->trouver_par_nomusager(($tokenDécodé->objetUser)->username);
                    return $user;
                }
            } catch (Exception $e) {
                return null;
            }
        });
    }
}
