DROP PROCEDURE IF EXISTS migration;
DELIMITER &&
  CREATE PROCEDURE migration()
  proc: BEGIN
		  SET @version := (SELECT `version` FROM `version`);
		  IF @version >= 3 THEN
			LEAVE proc;
		  END IF;

		  START TRANSACTION;
		

		  CREATE TABLE `commentaire` (
			`id`      	 	int NOT NULL ,
			`message`   		TEXT NOT NULL,
			`créateur`	 	varchar(255) NOT NULL,
			`date`	 	 	int(10) NOT NULL,
			`numéro_ligne`		int(10) NOT NULL,			
			`username`			varchar(255) NOT NULL,
			`question_uri` 		varchar(1024) CHARACTER SET latin1,
			`date_soumission` 	int(10) NOT NULL,
			PRIMARY KEY (`id`),
			FOREIGN KEY (`créateur`) REFERENCES user(`username`),
			FOREIGN KEY (`username`, `question_uri`, `date_soumission`) REFERENCES reponse_prog(`username`, `question_uri`, `date_soumission`)
			
		  );

		  UPDATE `version` SET `version` = 3;
		  COMMIT;

		END &&
DELIMITER ;

CALL migration();
DROP PROCEDURE migration;

