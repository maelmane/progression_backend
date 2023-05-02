DROP PROCEDURE IF EXISTS migration;
DELIMITER &&
  CREATE PROCEDURE migration()
  proc: BEGIN
		  SET @version := (SELECT `version` FROM `version`);
		  IF @version >= 10 THEN
			LEAVE proc;
		  END IF;

		  START TRANSACTION;

		  ALTER TABLE user ADD COLUMN `courriel` VARCHAR(255) NULL;
		  ALTER TABLE user RENAME COLUMN `actif` TO `etat`;

		  UPDATE `version` SET `version` = 10;
		  COMMIT;

		END &&
DELIMITER ;

CALL migration();
DROP PROCEDURE migration;

