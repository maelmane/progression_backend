#!/bin/bash

# Test unique nommé en paramètre
test_simple="$1"
DIR=$(dirname "${BASH_SOURCE[0]}")

# Création de la BD de test
echo Création de la BD de TEST sur $DB_SERVERNAME

> /dev/null $DIR/../db/build_db.sh && \
mysql --default-character-set=utf8 -h $DB_SERVERNAME -uroot -p$DB_PASSWORD $DB_DBNAME < $DIR/données_de_test.sql || exit 2

# Tests unitaires
if [ -z "$test_simple" ]
then
	$DIR/../vendor/bin/phpunit --configuration $DIR/../phpunit.xml --coverage-text  || exit 1
else
	$DIR/../vendor/bin/phpunit --configuration $DIR/../phpunit.xml --coverage-text --filter "$test_simple" || exit 1
fi

# Suppression de la BD
echo Suppression de la BD
echo "DROP DATABASE $DB_DBNAME" | mysql -h $DB_SERVERNAME -uroot -p$DB_PASSWORD || exit 2

# Analyseur statique, si on exécute tous les tests
if [ -z "$test_simple" ]
then
	echo Analyse statique
	php -d memory_limit=1G $DIR/../vendor/bin/phpstan analyse -c $DIR/../phpstan.neon || exit 1
fi
