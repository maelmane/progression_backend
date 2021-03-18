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

$router->get("/", function () use ($router) {
	return $router->app->version();
});

$router->post("/auth/", "LoginCtl@login");

$router->group(["middleware" => "auth"], function () use ($router) {
	$router->get("/user[/{username}]", "UserCtl@get");
	$router->get("/user/{username}/relationships/{relation}", "NotImplementedCtl@get");

	// catégories
	$router->get("/catégorie/{chemin}", "NotImplementedCtl@get");
	// Question
	$router->get("/question/{uri}", "QuestionCtl@get");
	$router->get("/question/{chemin}/relationships/{relation}", "NotImplementedCtl@get");
	// Avancement
	$router->get("/avancement/{username}/{question_uri}", "AvancementCtl@get");
	$router->get("/avancement/{username}/{chemin}/relationships/{relation}", "NotImplementedCtl@get");
	// Tentative
	$router->get("/tentative/{username}/{question_uri}/{timestamp:[[:digit:]]+}", "TentativeCtl@get");
	$router->get(
		"/tentative/{username}/{question_uri}/{timestamp:[[:digit:]]+}/relationships/{relation}",
		"NotImplementedCtl@get",
	);
	$router->post("/tentative/{username}/{question_uri}", "TentativeCtl@post");
	// Ébauche
	$router->get("/ebauche/{question_uri}/{langage}", "ÉbaucheCtl@get");
	// Test
	$router->get("/test/{question_uri}/{numero:[[:digit:]]+}", "TestCtl@get");
	// Résultat
	$router->post("/test/{username}/{question_uri}/{numero:[[:digit:]]+}", "NotImplementedCtl@get");
});
