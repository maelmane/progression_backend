#!/bin/bash
CUR_DIR=$(dirname $0)

echo Création de la BD de test sur $DB_SERVERNAME

$CUR_DIR/../../db/build_db.sh && \
mysql -h $DB_SERVERNAME -uroot -p$DB_PASSWORD $DB_DBNAME < $CUR_DIR/données_de_test.sql

echo Tests unitaires
$CUR_DIR/../vendor/bin/phpunit --configuration $CUR_DIR/../phpunit.xml --coverage-text || exit 1

echo Analyse statique
php -d memory_limit=1G $CUR_DIR/../vendor/bin/phpstan analyse -c $CUR_DIR/../phpstan.neon || exit 1
