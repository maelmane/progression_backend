DROP PROCEDURE IF EXISTS migration;
DELIMITER &&
  CREATE PROCEDURE migration()
  proc: BEGIN
		  SET @version := (SELECT `version` FROM `version`);
		  IF @version >= 12 THEN
			LEAVE proc;
		  END IF;

		  START TRANSACTION;

		  ALTER TABLE reponse_sys ADD COLUMN `url_terminal` varchar(255) NULL;
		  
		  UPDATE `version` SET `version` = 12;
		  COMMIT;

		END &&
DELIMITER ;

CALL migration();
DROP PROCEDURE migration;
