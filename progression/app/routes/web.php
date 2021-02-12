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
//$router->get('/solution/{username}/{question:.*\/}{timestamp:[[:digit:]]}', function() use ($router) {
$router->get('/solution/{question:.*}/{timestamp:[[:digit:]]}', function() use ($router) {
    return $question . " - " . $timestamp;
});
$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('/user[/{username}]', 'UserCtl@get');
    $router->get('/question/{id}', 'QuestionCtl@get');

    // catégories
    $router->get('/catégorie/{chemin:.*}', 'CatégorietCtl@get');
    // Question
    $router->get('/question/{chemin:.*}', 'QuestionCtl@get');
    // Avancement
    $router->get('/avancement/{username}/{question:.*}', 'AvancementCtl@get');
    // Tentative
    $router->get('/tentative/{username}/{question:.*}/{timestamp:[[:digit:]]}', 'TentativeCtl@get');
    $router->post('/solution/{username}/{question:.*}', 'SolutionCtl@get');
    // Solution
    $router->get('/solution/{username}/{question:.*}/{timestamp:[[:digit:]]}', 'SolutionCtl@get');
    $router->get('/ébauche/{question:.*}/', 'SolutionCtl@get');
    // Test
    $router->get('/test/{question:.*}/{numéro:[[:digit:]]}', 'TestCtl@get');
    // Résultat
    $router->post('/test/{username}/{question:.*}/{numéro:[[:digit:]]}', 'TestCtl@get');

});
