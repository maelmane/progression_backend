> /dev/null mysql --default-character-set=utf8 -v -h$DB_SERVERNAME -uroot -p$DB_PASSWORD <<EOF 
DROP DATABASE IF EXISTS $DB_DBNAME;

CREATE USER IF NOT EXISTS $DB_USERNAME@'%' IDENTIFIED BY "$DB_PASSWORD";
CREATE DATABASE $DB_DBNAME
	CHARACTER SET utf8mb4
	COLLATE utf8mb4_general_ci;

GRANT ALL PRIVILEGES ON $DB_DBNAME.* TO $DB_USERNAME@'%';

EOF

cat $(dirname $0)/create_db.sql | mysql --default-character-set=utf8 -v -h$DB_SERVERNAME -u$DB_USERNAME -p$DB_PASSWORD $DB_DBNAME > /dev/null
