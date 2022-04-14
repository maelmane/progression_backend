DROP PROCEDURE IF EXISTS migration;
DELIMITER &&
  CREATE PROCEDURE migration()
  proc: BEGIN
		  SET @version := (SELECT `version` FROM `version`);
		  IF @version >= 2 THEN
			LEAVE proc;
		  END IF;

		  START TRANSACTION;
		

		  CREATE TABLE `commentaire` (
			`username`			varchar(255) NOT NULL,
			`question_uri` 		varchar(1024) CHARACTER SET latin1,
			`date_soumission` 	int(10) NOT NULL,
			`numéro`      	 	int NOT NULL AUTO_INCREMENT ,
			`message`   		TEXT NOT NULL,
			`créateur`	 		varchar(255) NOT NULL,
			`date`	 	 		int(10) NOT NULL,
			`numéro_ligne`		int(10) NOT NULL,			
			PRIMARY KEY (`numéro`,`username`, `question_uri`, `date_soumission`),
			FOREIGN KEY (`créateur`) REFERENCES user(`username`),
			FOREIGN KEY (`username`, `question_uri`, `date_soumission`) REFERENCES reponse_prog(`username`, `question_uri`, `date_soumission`)
		  );
	
		  UPDATE `version` SET `version` = 2;
		  COMMIT;

		END &&
DELIMITER ;

CALL migration();
DROP PROCEDURE migration;

