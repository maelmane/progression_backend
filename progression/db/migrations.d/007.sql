DROP PROCEDURE IF EXISTS migration;
DELIMITER &&
  CREATE PROCEDURE migration()
  proc: BEGIN
		  SET @version := (SELECT `version` FROM `version`);
		  IF @version >= 7 THEN
			LEAVE proc;
		  END IF;

		  START TRANSACTION;

          ALTER TABLE `user`
			ADD COLUMN id int NOT NULL AUTO_INCREMENT,
			DROP PRIMARY KEY,
			ADD PRIMARY KEY(id),
			ADD INDEX (`username`);

          ALTER TABLE `avancement`
			ADD COLUMN id int NOT NULL AUTO_INCREMENT,
			ADD COLUMN user_id int NOT NULL,
			DROP COLUMN `type`,
			DROP PRIMARY KEY,
			ADD PRIMARY KEY(id),
			ADD INDEX(`username`, `question_uri`),
			ADD FOREIGN KEY (user_id) references user(id);

          ALTER TABLE `cle`
			ADD COLUMN id int NOT NULL AUTO_INCREMENT,
			ADD COLUMN user_id int NOT NULL,
			DROP PRIMARY KEY,
			ADD PRIMARY KEY(id),
			ADD INDEX(`username`, `nom`),
			ADD FOREIGN KEY (user_id) references user(id);

          ALTER TABLE `reponse_prog`
			ADD COLUMN id int NOT NULL AUTO_INCREMENT,
			ADD COLUMN avancement_id int NOT NULL,
			DROP PRIMARY KEY,
			ADD PRIMARY KEY(id),
			ADD INDEX(`username`, `question_uri`, `date_soumission`),
			ADD FOREIGN KEY (avancement_id) references avancement(id);
		  
          UPDATE `version` SET `version` = 7;
		  COMMIT;

		END &&
DELIMITER ;

CALL migration();
DROP PROCEDURE migration;
