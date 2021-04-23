<?php
require_once "/var/www/progression/vendor/" . 'autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable("/var/www/progression/app/");
$dotenv->load();

$dotenv->required('APP_URL')->allowedValues(['http://localhost/', 'http://172.20.0.3/']);
$dotenv->required('APP_NAME')->allowedValues(['Progression', 'Lumen']);
$dotenv->required('APP_ENV')->allowedValues(['local']);
$dotenv->required('APP_DEBUG')->isBoolean();
$dotenv->required('APP_TIMEZONE')->allowedValues(['UTC']);

$dotenv->required('DB_SERVERNAME')->allowedValues(['localhost', '172.20.0.2']);
$dotenv->required('DB_DBNAME')->allowedValues(['quiz']);
$dotenv->required('DB_USERNAME')->allowedValues(['root']);
$dotenv->required('DB_PASSWORD')->allowedValues(['password']);

$dotenv->required('AUTH_TYPE')->isBoolean();
$dotenv->required('HTTP_ORIGIN')->notEmpty();

$dotenv->required('LIMITE_YML')->isInteger();
$dotenv->required('JWT_TTL')->isInteger();

$dotenv->required('COMPILEBOX_URL')->allowedValues(['http://localhost:12380/', 'http://progression.dti.crosemont.quebec:12380/compile']);

$dotenv->required('LOG_CHANNEL')->allowedValues(['stack']);
$dotenv->required('CACHE_DRIVER')->allowedValues(['file']);
$dotenv->required('QUEUE_CONNECTION')->allowedValues(['sync']);

$dotenv->required(['JWT_SECRET', 'LOG_SLACK_WEBHOOK_URL', 'APP_KEY']);