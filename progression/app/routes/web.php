<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use progression\http\contrôleur\UserCtl;
use progression\http\contrôleur\LoginCtl;
use progression\http\contrôleur\QuestionCtl;

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

$router->post('/auth/', 'LoginCtl@login');

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('/user', 'UserCtl@get');
    $router->get('/question', 'QuestionCtl@get');
});
