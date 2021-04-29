<?php
require_once "/var/www/progression/vendor/" . 'autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable("/var/www/progression/app/");
$dotenv->load();

$dotenv->required('APP_URL')->allowedRegexValues('(.*/$)');
$dotenv->required('APP_NAME')->allowedRegexValues('(.*)');
$dotenv->required('APP_TIMEZONE')->allowedValues(['UTC']);

$dotenv->required('JWT_SECRET')->allowedRegexValues('(.*)');
$dotenv->required('DB_SERVERNAME')->allowedRegexValues('(.*)');
$dotenv->required('DB_DBNAME')->allowedRegexValues('(.*)');
$dotenv->required('DB_USERNAME')->allowedRegexValues('([a-zA-Z0-9_]+)');
$dotenv->required('DB_PASSWORD')->allowedRegexValues('(.*)');

$dotenv->required('AUTH_TYPE')->allowedRegexValues('(no|local|ldap)');
$dotenv->required('HTTP_ORIGIN')->allowedRegexValues('(.*)');

$dotenv->required('LIMITE_YML')->isInteger();
$dotenv->required('JWT_TTL')->isInteger();

$dotenv->required('COMPILEBOX_URL')->allowedRegexValues('(.*)');