DROP PROCEDURE IF EXISTS migration;
DELIMITER &&
  CREATE PROCEDURE migration()
  proc: BEGIN
		  SET @version := (SELECT `version` FROM `version`);
		  IF @version >= 13 THEN
			LEAVE proc;
		  END IF;

		  START TRANSACTION;

		  ALTER TABLE user
		  ADD COLUMN `prénom` VARCHAR(255) DEFAULT '' NULL,
		  ADD COLUMN `nom` VARCHAR(255) DEFAULT '' NULL,
		  ADD COLUMN `nom_complet` VARCHAR(255) DEFAULT '' NULL,
		  ADD COLUMN `biographie` TEXT DEFAULT '' NULL,
		  ADD COLUMN `pseudo` VARCHAR(255) DEFAULT '' NOT NULL,
		  ADD COLUMN `avatar` VARCHAR(255) DEFAULT '' NULL,
		  ADD COLUMN `connaissances` VARCHAR(255) DEFAULT '' NULL,
		  ADD COLUMN `occupation` INT(11) DEFAULT 1 NULL;

		  UPDATE `user` SET `pseudo` = `username` WHERE `pseudo` = '';
		  
		  UPDATE `version` SET `version` = 13;
		  COMMIT;

		END &&
DELIMITER ;

CALL migration();
DROP PROCEDURE migration;
