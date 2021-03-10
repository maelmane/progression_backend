DROP DATABASE IF EXISTS quiz;
DROP USER IF EXISTS quiz@localhost;
DROP USER IF EXISTS 'quiz'@'%';
CREATE USER 'quiz'@'%' IDENTIFIED BY 'password';
CREATE DATABASE quiz
	CHARACTER SET utf8mb4
	COLLATE utf8mb4_general_ci;
USE quiz;
CREATE TABLE `user` (
	`username`	varchar(255),
	`courriel` 	varchar(255),
	`actif`		int NOT NULL DEFAULT 1,
	`role`		int NOT NULL DEFAULT 0,
	PRIMARY KEY (`username`)
);
CREATE TABLE `avancement` (
	`username`		varchar(255) NOT NULL,
	`question_uri` 	varchar(2048) CHARACTER SET latin1 NOT NULL,
	`etat`			int DEFAULT 1,
	`sous_type`		int NOT NULL,
	PRIMARY KEY (`username`, `question_uri`),
	FOREIGN KEY ( `username`) REFERENCES `user`(`username`)
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
	`question_uri` 		varchar(2048) character set latin1,
	`date_soumission` 	int(10) NOT NULL,
	`langage`			int NOT NULL,
	`code`				text,
	PRIMARY KEY (`username`, `question_uri`, `langage`),
	FOREIGN KEY (`username`, `question_uri`) REFERENCES avancement(`username`, `question_uri`)
);
/*Admin*/
GRANT ALL PRIVILEGES ON quiz.* TO quiz@'%';
