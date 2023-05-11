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

$router->options("{all:.*}", [
	"middleware" => "cors",
	function () {
		return response("");
	},
]);

$router->get("/", function () use ($router) {
	return config("app.name") . " " . config("version.numéro") . "(" . config("version.commit_sha") . ")";
});

// Configuration serveur
$router->get("/config", "ConfigCtl@get");

// Inscription
$router->put("/user/{username}", "UserCréationCtl@put");

// Authentification
$router->post("/auth/", "LoginCtl@login");

$router->group(["middleware" => ["auth", "étatNonInactif"]], function () use ($router) {
	// Ébauche
	$router->get("/ebauche/{question_uri}/{langage}", "ÉbaucheCtl@get");

	// Question
	$router->get("/question/{uri}", "QuestionCtl@get");
	$router->get("/question/{uri}/relationships/ebauches", "NotImplementedCtl@get");
	$router->get("/question/{uri}/relationships/tests", "NotImplementedCtl@get");
	$router->get("/question/{uri}/ebauches", "NotImplementedCtl@get");
	$router->get("/question/{uri}/tests", "NotImplementedCtl@get");

	// Test
	$router->get("/test/{question_uri}/{numero:[[:digit:]]+}", "TestCtl@get");

	// Résultat
	$router->put("/resultat", "RésultatCtl@put");
});

$router->group(["middleware" => ["auth", "étatNonInactif", "permissionsRessources"]], function () use ($router) {
	// Avancement
	$router->get("/avancement/{username}/{question_uri}", "AvancementCtl@get");
	$router->get("/avancement/{username}/{chemin}/relationships/tentatives", "NotImplementedCtl@get");
	$router->get("/avancement/{username}/{chemin}/tentatives", "NotImplementedCtl@get");
	$router->post("/user/{username}/avancements", "AvancementCtl@post");

	// Avancements
	$router->get("/user/{username}/avancements", "AvancementsCtl@get");

	// Clé
	$router->post("/user/{username}/cles", "CléCtl@post");
	$router->get("/cle/{username}/{nom}", "CléCtl@get");

	// Commentaire
	$router->post(
		"/tentative/{username}/{question_uri}/{timestamp:[[:digit:]]{10}}/commentaires",
		"CommentaireCtl@post",
	);
	$router->get(
		"/commentaire/{username}/{question_uri}/{timestamp:[[:digit:]]{10}}/{numero}",
		"NotImplementedCtl@get",
	);

	// Résultat
	$router->post("/test/{username}/{question_uri}/{numero:[[:digit:]]+}", "NotImplementedCtl@get");

	// Sauvegarde
	$router->post("/avancement/{username}/{question_uri}/sauvegardes", "SauvegardeCtl@post");
	$router->get("/sauvegarde/{username}/{question_uri}/{langage}", "SauvegardeCtl@get");
	$router->get("/avancement/{username}/{question_uri}/sauvegardes", "NotImplementedCtl@get");

	// Tentative
	$router->post("/avancement/{username}/{question_uri}/tentatives", "TentativeCtl@post");
	$router->get("/tentative/{username}/{question_uri}/{timestamp:[[:digit:]]{10}}", "TentativeCtl@get");
	$router->get(
		"/tentative/{username}/{question_uri}/{timestamp:[[:digit:]]{10}}/relationships/resultats",
		"NotImplementedCtl@get",
	);
	$router->get("/tentative/{username}/{question_uri}/{timestamp:[[:digit:]]{10}}/resultats", "NotImplementedCtl@get");

	// Token
	$router->post("/token/{username}/", "TokenCtl@post");

	// User
	$router->get("/user/{username}", "UserCtl@get");
	$router->post("/user/{username}", "UserCtl@post");
	$router->get("/user/{username}/relationships/avancements", "NotImplementedCtl@get");
});
