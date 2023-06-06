DROP PROCEDURE IF EXISTS migration;
DELIMITER &&
  CREATE PROCEDURE migration()
  proc: BEGIN
		  SET @version := (SELECT `version` FROM `version`);
		  IF @version >= 11 THEN
			LEAVE proc;
		  END IF;

		  START TRANSACTION;

		  ALTER TABLE user ADD COLUMN `date_inscription` INT NULL;
		  
		  UPDATE `version` SET `version` = 11;
		  COMMIT;

		END &&
DELIMITER ;

CALL migration();
DROP PROCEDURE migration;
