<?php
require_once "/var/www/progression/vendor/" . 'autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable("/var/www/progression/app/");
$dotenv->load();

$dotenv->required('APP_URL')->allowedRegexValues('(.*)');
$dotenv->required('APP_NAME')->allowedValues(['Progression']);
$dotenv->required('APP_ENV')->allowedValues(['local']);
$dotenv->required('APP_TIMEZONE')->allowedValues(['UTC']);

$dotenv->required('JWT_SECRET')->allowedRegexValues('(.*)');
$dotenv->required('DB_SERVERNAME')->allowedRegexValues('(.*)');
$dotenv->required('DB_DBNAME')->allowedRegexValues('(.*)');
$dotenv->required('DB_USERNAME')->allowedRegexValues('([A-z]+)');
$dotenv->required('DB_PASSWORD')->allowedRegexValues('(.*)');

$dotenv->required('AUTH_TYPE')->allowedRegexValues('(no|local|ldap)');
$dotenv->required('HTTP_ORIGIN')->allowedRegexValues('(.*)');

$dotenv->required('LIMITE_YML')->isInteger();
$dotenv->required('JWT_TTL')->isInteger();

$dotenv->required('COMPILEBOX_URL')->allowedRegexValues('(.*)');

$dotenv->required('APP_DEBUG')->isBoolean();
$dotenv->required('LOG_CHANNEL')->allowedValues(['stack']);
$dotenv->required('CACHE_DRIVER')->allowedValues(['file']);
$dotenv->required('QUEUE_CONNECTION')->allowedValues(['sync']);

$dotenv->required(['LOG_SLACK_WEBHOOK_URL', 'APP_KEY']);