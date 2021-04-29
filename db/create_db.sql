CREATE TABLE `user` (
	`username`	varchar(255),
	`actif`		int NOT NULL DEFAULT 1,
	`role`		int NOT NULL DEFAULT 0,
	PRIMARY KEY (`username`)
);
CREATE TABLE `sauvegarde` (
	`id`			int NOT NULL AUTO_INCREMENT,
	`code`			text NOT NULL,
	`langage`		varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
);
CREATE TABLE `avancement` (
	`username`		varchar(255) NOT NULL,
	`question_uri` 	varchar(2048) CHARACTER SET latin1 NOT NULL,
	`etat`			int DEFAULT 1,
	`type`          int NOT NULL,
	`sauvegarde_id`	int,
	PRIMARY KEY (`username`, `question_uri`),
	FOREIGN KEY (`username`) REFERENCES `user`(`username`),
	FOREIGN KEY (`sauvegarde_id`) REFERENCES `sauvegarde`(`id`)
);
CREATE TABLE `reponse_sys` (
	`username`		varchar(255) NOT NULL,
	`question_uri` 	varchar(2048) CHARACTER SET latin1,
	`conteneur`		varchar(64),
	`reponse`		varchar(255),
	PRIMARY KEY (`username`, `question_uri`),
	FOREIGN KEY ( `username`) REFERENCES `user`(`username`),
	FOREIGN KEY (`username`, `question_uri`) REFERENCES avancement(`username`, `question_uri`)
);
CREATE TABLE `reponse_prog` (
	`username`			varchar(255) NOT NULL,
	`question_uri` 		varchar(2048) CHARACTER SET latin1,
	`date_soumission` 	int(10) NOT NULL,
	`langage`			varchar(255) NOT NULL,
	`code`				text,
	`reussi`            boolean NOT NULL DEFAULT false,
	`tests_reussis`  int NOT NULL DEFAULT 0,
	PRIMARY KEY (`username`, `question_uri`, `date_soumission`),
	FOREIGN KEY (`username`, `question_uri`) REFERENCES avancement(`username`, `question_uri`)
);
