<?php
require_once __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dotenv->required("APP_URL")->allowedRegexValues('(.*/$)');
$dotenv->required("APP_NAME")->allowedRegexValues("(.*)");
$dotenv->required("APP_TIMEZONE")->allowedValues(["UTC"]);

$dotenv->required("JWT_SECRET")->allowedRegexValues("(.*)");
$dotenv->required("DB_SERVERNAME")->allowedRegexValues("(.*)");
$dotenv->required("DB_DBNAME")->allowedRegexValues("(.*)");
$dotenv->required("DB_USERNAME")->allowedRegexValues("([a-zA-Z0-9_]+)");
$dotenv->required("DB_PASSWORD")->allowedRegexValues("(.*)");

$dotenv->ifpresent("AUTH_LOCAL")->isBoolean();
$dotenv->ifpresent("AUTH_LDAP")->isBoolean();

$dotenv->required("HTTP_ORIGIN")->allowedRegexValues("(.*)");

$dotenv->required("QUESTION_TAILLE_MAX")->isInteger();
$dotenv->required("JWT_TTL")->isInteger();

$dotenv->required("COMPILEBOX_URL")->allowedRegexValues("(.*)");
