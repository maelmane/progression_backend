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

$router->options("{all:.*}", [
	"middleware" => "cors",
	function () {
		return response("");
	},
]);

$router->post("/auth/", "LoginCtl@login");

$router->group(["middleware" => "auth"], function () use ($router) {
	// Question
	$router->get("/question/{uri}", "QuestionCtl@get");
	$router->get("/question/{uri}/relationships/ebauches", "NotImplementedCtl@get");
	$router->get("/question/{uri}/relationships/tests", "NotImplementedCtl@get");
	$router->get("/question/{uri}/ebauches", "NotImplementedCtl@get");
	$router->get("/question/{uri}/tests", "NotImplementedCtl@get");
	// Ébauche
	$router->get("/ebauche/{question_uri}/{langage}", "ÉbaucheCtl@get");
	// Test
	$router->get("/test/{question_uri}/{numero:[[:digit:]]+}", "TestCtl@get");
});

$router->group(["middleware" => ["auth", "validationPermissions"]], function () use ($router) {
	// User
	$router->get("/user[/{username}]", "UserCtl@get");
	$router->get("/user/{username}/relationships/avancements", "NotImplementedCtl@get");
	$router->get("/user/{username}/avancements", "NotImplementedCtl@get");
	// Sauvegarde
	$router->post("/avancement/{username}/{question_uri}/sauvegardes", "SauvegardeCtl@post");
	$router->get("/sauvegarde/{username}/{question_uri}/{langage}", "SauvegardeCtl@get");
	$router->get("/avancement/{username}/{question_uri}/sauvegardes", "NotImplementedCtl@get");
	// Avancement
	$router->post("/user/{username}/avancements", "AvancementCtl@post");
	$router->get("/avancement/{username}/{question_uri}", "AvancementCtl@get");
	$router->get("/avancement/{username}/{chemin}/relationships/tentatives", "NotImplementedCtl@get");
	$router->get("/avancement/{username}/{chemin}/tentatives", "NotImplementedCtl@get");
	// Tentative
	$router->post("/tentative/{username}/{question_uri}", "TentativeCtl@post");
	$router->get("/tentative/{username}/{question_uri}/{timestamp:[[:digit:]]{10}}", "TentativeCtl@get");
	$router->get(
		"/tentative/{username}/{question_uri}/{timestamp:[[:digit:]]{10}}/relationships/resultats",
		"NotImplementedCtl@get",
	);
	$router->get("/tentative/{username}/{question_uri}/{timestamp:[[:digit:]]{10}}/resultats", "NotImplementedCtl@get");
	// Résultat
	$router->post("/test/{username}/{question_uri}/{numero:[[:digit:]]+}", "NotImplementedCtl@get");
});
