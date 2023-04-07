#!/bin/bash

# Création initiale
>/dev/null mysql --default-character-set=utf8 -h $DB_HOST -v -uroot -p$MYSQL_ROOT_PASSWORD <<EOF
CREATE DATABASE IF NOT EXISTS $DB_DATABASE
				  CHARACTER SET utf8mb4
				  COLLATE utf8mb4_general_ci;
show databases;
USE $DB_DATABASE;
DROP PROCEDURE IF EXISTS migration;

DELIMITER &&
CREATE PROCEDURE migration()
    proc: BEGIN

    CREATE TABLE IF NOT EXISTS version (
    	version int NOT NULL DEFAULT 0
    );

    SET @version := (SELECT count(version) FROM version);
    IF @version > 0 THEN
    	LEAVE proc;
    END IF;
    
    START TRANSACTION;
    
    CREATE USER IF NOT EXISTS $DB_USERNAME@'%' IDENTIFIED BY "$DB_PASSWORD";
    
    GRANT ALL PRIVILEGES ON $DB_DATABASE.* TO $DB_USERNAME@'%';
    
    INSERT INTO version VALUES(0);
    
    COMMIT;

END&&
DELIMITER ;

CALL migration();
DROP PROCEDURE migration;

EOF

# Migrations
wd=$(dirname ${BASH_SOURCE[0]})

for migration in $(ls $wd/migrations.d/[0-9]*.sql)
do
	echo -n Migration $migration...
	>/dev/null mysql --default-character-set=utf8 -v -h $DB_HOST -u$DB_USERNAME -p$DB_PASSWORD $DB_DATABASE < $migration && echo OK
done
