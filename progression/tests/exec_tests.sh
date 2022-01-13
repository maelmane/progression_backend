#!/bin/bash

echo Création de la BD de test sur $DB_SERVERNAME

$PROGRESSION_DIR/db/build_db.sh && \
mysql --default-character-set=utf8 -h $DB_SERVERNAME -uroot -p$DB_PASSWORD $DB_DBNAME < $PROGRESSION_DIR/progression/tests/données_de_test.sql || exit 2

$PROGRESSION_DIR/progression/vendor/bin/phpunit --configuration $PROGRESSION_DIR/progression/phpunit.xml --coverage-text || exit 1

echo Suppression de la BD
echo "DROP DATABASE $DB_DBNAME" | mysql -h $DB_SERVERNAME -uroot -p$DB_PASSWORD || exit 2

echo Analyse statique
php -d memory_limit=1G $PROGRESSION_DIR/progression/vendor/bin/phpstan analyse -c $PROGRESSION_DIR/progression/phpstan.neon || exit 1
