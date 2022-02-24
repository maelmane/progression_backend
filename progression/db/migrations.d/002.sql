DROP PROCEDURE IF EXISTS migration;
DELIMITER &&
  CREATE PROCEDURE migration()
  proc: BEGIN
		  SET @version := (SELECT `version` FROM `version`);
		  IF @version >= 1 THEN
			LEAVE proc;
		  END IF;

		  START TRANSACTION;
		  
          
            ALTER TABLE `avancement`
            ADD (titre varchar(255) NOT NULL DEFAULT "", 
                niveau varchar(255) NOT NULL DEFAULT "",
                date_modification int(10) NOT NULL,
                date_reussite int(10));


          UPDATE `version` SET `version` = 1;
		  COMMIT;

		END &&
DELIMITER ;

CALL migration();
DROP PROCEDURE migration;

