DROP DATABASE IF EXISTS quiz;
DROP USER IF EXISTS quiz@localhost;

DROP USER IF EXISTS 'quiz'@'%';
CREATE USER 'quiz'@'%' IDENTIFIED BY 'password';
CREATE DATABASE quiz
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE quiz;

CREATE TABLE `users` (
  `userID`   int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(191) NOT NULL UNIQUE,
  `courriel` varchar(255),
  `actif`    int NOT NULL DEFAULT 1,
  `role`     int NOT NULL DEFAULT 0,

  PRIMARY KEY (`userID`)
);

CREATE TABLE `theme` (
  `themeID`     int(11) NOT NULL AUTO_INCREMENT,
  `actif`       int NOT NULL DEFAULT 1,
  `lang`	int,
  `titre`       varchar(255) NOT NULL,
  `ordre`       int,
  `description` text,
  PRIMARY KEY (`themeID`)
);

CREATE TABLE `serie` (
  `serieID`     int(11) NOT NULL AUTO_INCREMENT,
  `themeID`	int(11) NOT NULL,
  `actif`       int NOT NULL DEFAULT 1,
  `numero`	int NOT NULL,
  `titre`       varchar(255) NOT NULL,
  `description` text,
  PRIMARY KEY (`serieID`)
);

CREATE TABLE `question` (
  `questionID`	int(11) NOT NULL AUTO_INCREMENT,
  `serieID`	int(11) NOT NULL,
  `actif`       int NOT NULL DEFAULT 1,
  `type`        int NOT NULL,
  `titre`       varchar(255) NOT NULL,
  `description`	text,
  `numero`	int NOT NULL,
  `enonce`	text,
  `points`	int DEFAULT 0,
  `code_validation`varchar(64),
  PRIMARY KEY (`questionID`)
);

CREATE TABLE `question_prog` (
  `questionID`  int(11) NOT NULL,
  `lang`	int,
  `setup`	text,
  `pre_exec`	text,
  `pre_code`	text,
  `in_code`	text,
  `post_code`	text,
  `reponse`	text,
  `params`	varchar(255),
  `stdin`	varchar(255),
  PRIMARY KEY (`questionID`)
);

CREATE TABLE `question_systeme` (
  `questionID`    int(11) NOT NULL,
  `image`         varchar(255),
  `user`          varchar(32),
  `verification`  text,
  `reponse`       varchar(255),
  PRIMARY KEY (`questionID`)
);


CREATE TABLE `avancement` (
  `userID`       int(11) NOT NULL,
  `questionID`   int(11) NOT NULL,
  `conteneur`    varchar(64),
  `etat`	 int DEFAULT 1,
  `reponse`	 text,
  PRIMARY KEY (`userID`, `questionID`)
);

/*Admin*/


GRANT ALL PRIVILEGES ON quiz.* TO quiz@'%';

