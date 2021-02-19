<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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
    $router->get('/user[/{username}]', 'UserCtl@get');
    $router->get('/user/{username}/relationships/{relation}', 'NotImplementedCtl@get');

    // catégories
    $router->get('/catégorie/{chemin}', 'NotImplementedCtl@get');
    // Question
    $router->get('/question/{chemin}', 'QuestionProgCtl@get');
    $router->get('/question/{chemin}/relationships/{relation}', 'NotImplementedCtl@get');
    // Avancement
    $router->get('/avancement/{username}/{question}', 'AvancementProgCtl@get');
    $router->get('/avancement/{username}/{question}/relationships/{relation}', 'NotImplementedCtl@get');
    // Tentative
    $router->get('/tentative/{username}/{question}/{timestamp:[[:digit:]]+}', 'NotImplementedCtl@get');
    $router->get('/tentative/{username}/{question}/{timestamp:[[:digit:]]+}/relationships/{relation}', 'NotImplementedCtl@get');
    // Solution
    $router->post('/solution/{username}/{question}', 'NotImplementedCtl@get');
    $router->get('/solution/{question}/', 'NotImplementedCtl@get');
    $router->get('/solution/{username}/{question}/{timestamp:[[:digit:]]+}', 'NotImplementedCtl@get');
    // Test
    $router->get('/test/{question}/{numero:[[:digit:]]+}', 'NotImplementedCtl@get');
    // Résultat
    $router->post('/test/{username}/{question}/{numero:[[:digit:]]+}', 'NotImplementedCtl@get');

});
