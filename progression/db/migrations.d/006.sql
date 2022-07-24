DROP PROCEDURE IF EXISTS migration;
DELIMITER &&
  CREATE PROCEDURE migration()
  proc: BEGIN
		  SET @version := (SELECT `version` FROM `version`);
		  IF @version >= 6 THEN
			LEAVE proc;
		  END IF;

		  START TRANSACTION;
		
          DROP TABLE `reponse_sys`;
		  CREATE TABLE `reponse_sys` (
			`username`			varchar(255) NOT NULL,
			`question_uri` 		varchar(1024) CHARACTER SET latin1,
            `conteneur`		    varchar(64),
			`reponse`		    varchar(255),
			`date_soumission` 	int(10) NOT NULL,
			`reussi`            boolean NOT NULL DEFAULT false,
			`tests_reussis`     int NOT NULL DEFAULT 0,
            `temps_exécution`   int(10) NOT NULL,
			PRIMARY KEY (`username`, `question_uri`, `date_soumission`),
			FOREIGN KEY (`username`, `question_uri`) REFERENCES avancement(`username`, `question_uri`)
		  );

		  UPDATE `version` SET `version` = 6;
		  COMMIT;

		END &&
DELIMITER ;

CALL migration();
DROP PROCEDURE migration;
