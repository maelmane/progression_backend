#!/bin/bash
echo Création de la BD de TEST sur $DB_SERVERNAME

DIR=$(dirname "${BASH_SOURCE[0]}")

$DIR/../db/build_db.sh && \
mysql --default-character-set=utf8 -h $DB_SERVERNAME -uroot -p$DB_PASSWORD $DB_DBNAME < $DIR/données_de_test.sql || exit 2

$DIR/../vendor/bin/phpunit --configuration $DIR/../phpunit.xml --coverage-text || exit 1

echo Suppression de la BD
echo "DROP DATABASE $DB_DBNAME" | mysql -h $DB_SERVERNAME -uroot -p$DB_PASSWORD || exit 2

echo Analyse statique
php -d memory_limit=1G $DIR/../vendor/bin/phpstan analyse -c $DIR/../phpstan.neon || exit 1
