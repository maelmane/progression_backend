#!/bin/bash
echo Création de la BD de test sur $DB_SERVERNAME

../db/build_db.sh && \
mysql --default-character-set=utf8 -h $DB_SERVERNAME -uroot -p$DB_PASSWORD $DB_DBNAME < ./données_de_test.sql || exit 2

../vendor/bin/phpunit --configuration ../phpunit.xml --coverage-text || exit 1

echo Suppression de la BD
echo "DROP DATABASE $DB_DBNAME" | mysql -h $DB_SERVERNAME -uroot -p$DB_PASSWORD || exit 2

echo Analyse statique
php -d memory_limit=1G ../vendor/bin/phpstan analyse -c ../phpstan.neon || exit 1
