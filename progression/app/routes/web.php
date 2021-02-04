<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use progression\http\controllers\CalculatriceController;
use progression\http\controllers\ThemeControleur;
use progression\http\controllers\UtilisateurControleur;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/user/connexion/', 'LoginControleur@login');

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('themes', 'ThemeControleur@getThemes');
});
