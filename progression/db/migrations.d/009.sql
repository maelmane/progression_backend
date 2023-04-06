DROP PROCEDURE IF EXISTS migration;
DELIMITER &&
  CREATE PROCEDURE migration()
  proc: BEGIN
		  SET @version := (SELECT `version` FROM `version`);
		  IF @version >= 9 THEN
			LEAVE proc;
		  END IF;

		  START TRANSACTION;

		  ALTER TABLE user ADD COLUMN `preferences` TEXT NULL;

		  UPDATE `version` SET `version` = 9;
		  COMMIT;

		END &&
DELIMITER ;

CALL migration();
DROP PROCEDURE migration;

