DROP PROCEDURE IF EXISTS migration;
DELIMITER &&
  CREATE PROCEDURE migration()
  proc: BEGIN
		  SET @version := (SELECT `version` FROM `version`);
		  IF @version >= 8 THEN
			LEAVE proc;
		  END IF;

		  START TRANSACTION;
		
		  ALTER TABLE avancement ADD COLUMN `extra` VARCHAR(2048) NULL;
	
		  UPDATE `version` SET `version` = 8;
		  COMMIT;

		END &&
DELIMITER ;

CALL migration();
DROP PROCEDURE migration;

