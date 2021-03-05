DROP DATABASE IF EXISTS quiz;
DROP USER IF EXISTS quiz@localhost;

DROP USER IF EXISTS 'quiz'@'%';
CREATE USER 'quiz'@'%' IDENTIFIED BY 'password';
CREATE DATABASE quiz
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE quiz;

CREATE TABLE `user` (
  `user_id`  varchar(255),
  `courriel` varchar(255),
  `actif`    int NOT NULL DEFAULT 1,
  `role`     int NOT NULL DEFAULT 0,

  PRIMARY KEY (`username`)
);

CREATE TABLE `avancement` (
  `user_id`      int(11) NOT NULL,
  `question_uri` varchar(4096) NOT NULL,
  `etat`	     int DEFAULT 1,
  `sous_type`    int NOT NULL,
  PRIMARY KEY (`user_id`, `question_uri`),
  FOREIGN KEY ( `user_id`) REFERENCES `user`.`id`
);

CREATE TABLE `reponse_sys` (
  `user_id`      int(11) NOT NULL,
  `question_uri` varchar(4096) NOT NULL,
  `conteneur`    varchar(64),
  `reponse`      varchar(255),
  PRIMARY KEY (`user_id`, `question_uri`),
  FOREIGN KEY ( `user_id`) REFERENCES `user`.`id`
);

CREATE TABLE `reponse_prog` (
  `user_id`      int(11) NOT NULL,
  `question_uri` varchar(4096) NOT NULL,
  `date_soumission` int(10) NOT NULL,
  `langage`      int NOT NULL,
  `code`	     text,
  PRIMARY KEY (`user_id`, `question_uri`, `langage`),
  FOREIGN KEY ( `user_id`) REFERENCES `user`.`id`
  );

/*Admin*/


GRANT ALL PRIVILEGES ON quiz.* TO quiz@'%';
