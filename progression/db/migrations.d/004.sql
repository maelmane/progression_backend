DROP PROCEDURE IF EXISTS migration;
DELIMITER &&
  CREATE PROCEDURE migration()
  proc: BEGIN
		  SET @version := (SELECT `version` FROM `version`);
		  IF @version >= 4 THEN
			LEAVE proc;
		  END IF;

		  START TRANSACTION;
		  
          ALTER TABLE `reponse_prog`
          ADD `temps_exécution` int(10) NOT NULL;

		  UPDATE `version` SET `version` = 4;
		  COMMIT;

		END &&
DELIMITER ;

CALL migration();
DROP PROCEDURE migration;