DROP PROCEDURE IF EXISTS migration;
DELIMITER &&
  CREATE PROCEDURE migration()
  proc: BEGIN
          SET @version := (SELECT `version` FROM `version`);
          IF @version >= 7 THEN
            LEAVE proc;
          END IF;

          START TRANSACTION;

		  # Remplacement des clés primaires par des clés de substitution
		  
          # Commentaire
          ALTER TABLE `commentaire`
            CHANGE COLUMN numéro id int NOT NULL AUTO_INCREMENT,
            ADD COLUMN tentative_id int NOT NULL,
            ADD COLUMN créateur_id int NOT NULL,
            DROP FOREIGN KEY commentaire_ibfk_1,
            DROP FOREIGN KEY commentaire_ibfk_2,
            DROP PRIMARY KEY,
            ADD PRIMARY KEY(id);
          
          # Tentative Prog
          ALTER TABLE `reponse_prog`
            ADD COLUMN id int NOT NULL AUTO_INCREMENT,
            ADD COLUMN avancement_id int NOT NULL,
            DROP FOREIGN KEY reponse_prog_ibfk_1,
            DROP PRIMARY KEY,
            ADD PRIMARY KEY(id);

          # Tentative Sys
          ALTER TABLE `reponse_sys`
            ADD COLUMN id int NOT NULL AUTO_INCREMENT,
            ADD COLUMN avancement_id int NOT NULL,
            DROP FOREIGN KEY reponse_sys_ibfk_1,
            DROP PRIMARY KEY,
            ADD PRIMARY KEY(id);

          # Sauvegarde
          ALTER TABLE `sauvegarde`
            ADD COLUMN id int NOT NULL AUTO_INCREMENT,
            ADD COLUMN avancement_id int NOT NULL,
            DROP PRIMARY KEY,
            DROP FOREIGN KEY sauvegarde_ibfk_1,
            ADD PRIMARY KEY(id);

          # Avancement
          ALTER TABLE `avancement`
            ADD COLUMN id int NOT NULL AUTO_INCREMENT,
            ADD COLUMN user_id int NOT NULL,
            DROP COLUMN type,
            ADD COLUMN type varchar(255) NOT NULL DEFAULT "prog",
            DROP PRIMARY KEY,
            DROP FOREIGN KEY avancement_ibfk_1,
            ADD PRIMARY KEY(id);

          # Clé
          ALTER TABLE `cle`
            ADD COLUMN id int NOT NULL AUTO_INCREMENT,
            ADD COLUMN user_id int NOT NULL,
            DROP PRIMARY KEY,
            DROP FOREIGN KEY cle_ibfk_1,
            ADD PRIMARY KEY(id);

          # User
          ALTER TABLE `user`
            ADD COLUMN id int NOT NULL AUTO_INCREMENT,
            DROP PRIMARY KEY,
            ADD PRIMARY KEY(id),
            ADD UNIQUE INDEX (`username`);
          
          # Commentaire
          UPDATE `commentaire` AS c
            SET
            tentative_id = (SELECT id FROM `reponse_prog` WHERE username = c.username AND question_uri = c.question_uri AND date_soumission = c.date_soumission),
            créateur_id = (SELECT id FROM `user` WHERE username = c.créateur);

          ALTER TABLE `commentaire`
            DROP COLUMN username,
            DROP COLUMN question_uri,
            DROP COLUMN date_soumission,
            DROP COLUMN créateur,
            ADD FOREIGN KEY fk_commentaire_tentative_prog (tentative_id) references reponse_prog(id);
          
          # Tentative Prog
          UPDATE `reponse_prog` AS rp
             SET avancement_id = (SELECT id FROM `avancement` WHERE username = rp.username AND question_uri = rp.question_uri);

          ALTER TABLE `reponse_prog`
            DROP COLUMN username,
            DROP COLUMN question_uri,
            ADD UNIQUE INDEX(`avancement_id`, `date_soumission`),
            ADD FOREIGN KEY fk_tentative_prog_avancement (avancement_id) references avancement(id);

          # Tentative Sys
          UPDATE `reponse_sys` AS rs
             SET avancement_id = (SELECT id FROM `avancement` WHERE username = rs.username AND question_uri = rs.question_uri);

          ALTER TABLE `reponse_sys`
            DROP COLUMN username,
            DROP COLUMN question_uri,
            ADD FOREIGN KEY fk_tentative_sys_avancement (avancement_id) references avancement(id);

          # Sauvegarde
          UPDATE `sauvegarde` AS s
             SET avancement_id = (SELECT id FROM `avancement` WHERE username = s.username AND question_uri = s.question_uri);

          ALTER TABLE `sauvegarde`
            DROP COLUMN username,
            DROP COLUMN question_uri,
            ADD UNIQUE INDEX (`avancement_id`, `langage`),
            ADD FOREIGN KEY fk_sauvegarde_avancement (avancement_id) references avancement(id);

          # Avancement
          UPDATE `avancement` AS a
            SET user_id = (SELECT id FROM `user` WHERE username = a.username AND question_uri = a.question_uri);
                 
          ALTER TABLE `avancement`
            DROP COLUMN `username`,
            ADD UNIQUE INDEX(`user_id`, `question_uri`),
            ADD FOREIGN KEY fk_avancement_user (user_id) references user(id);

          # Clé
          UPDATE `cle`AS c
             SET user_id = (SELECT id FROM `user` WHERE username = c.username);

          ALTER TABLE `cle`
            DROP COLUMN `username`,
            ADD UNIQUE INDEX(`user_id`, `nom`),
            ADD FOREIGN KEY fb_cle_user (user_id) references user(id);
          
          UPDATE `version` SET `version` = 7;
          COMMIT;

        END &&
DELIMITER ;

CALL migration();
DROP PROCEDURE migration;
