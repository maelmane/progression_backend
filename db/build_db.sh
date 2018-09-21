cat create_db.sql populate_db.sql | mysql mysql --default-character-set=utf8 -v -u root -ppassword > /dev/null
