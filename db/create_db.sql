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
  `nom`         varchar(255) NOT NULL UNIQUE,
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
  `nom`         varchar(255) NOT NULL,
  `actif`       int NOT NULL DEFAULT 1,
  `numero`	int NOT NULL,
  `titre`       varchar(255) NOT NULL,
  `description` text,
  PRIMARY KEY (`serieID`),
  UNIQUE KEY(`themeID`, `nom`)
);

CREATE TABLE `question` (
  `questionID`	int(11) NOT NULL AUTO_INCREMENT,
  `serieID`	int(11) NOT NULL,
  `nom`         varchar(255) NOT NULL,
  `chemin`      varchar(15000) NOT NULL,
  `actif`       int NOT NULL DEFAULT 1,
  `type`        int NOT NULL,
  `titre`       varchar(255) NOT NULL,
  `description`	text,
  `numero`	int NOT NULL,
  `enonce`	text,
  `feedback_pos` text,
  `feedback_neg` text,
  `code_validation`varchar(64),
  PRIMARY KEY (`questionID`),
  UNIQUE KEY(`serieID`, `nom`)
  
);

CREATE TABLE `executable` (
  `questionID` int(11) NOT NULL,
  `code` text NOT NULL,
  `lang` int NOT NULL,
  PRIMARY KEY (`questionID`, `lang`)
  );

CREATE TABLE `test` (
  `questionID` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `stdin` text NOT NULL,
  `params` text,
  `solution` text,
  `feedback_pos` text,
  `feedback_neg` text,
  PRIMARY KEY (`questionID`, `nom`)
  );

CREATE TABLE `question_prog_eval` (
  `questionID`  int(11) NOT NULL,
  `lang`	int,
  `setup`	text,
  `pre_exec`	text,
  `pre_code`	text,
  `in_code`	text,
  `post_code`	text,
  `solution`	text,
  `params`	varchar(255),
  `stdin`	varchar(255),
  PRIMARY KEY (`questionID`)
);

CREATE TABLE `question_systeme` (
  `questionID`    int(11) NOT NULL,
  `image`         varchar(255),
  `user`          varchar(32),
  `verification`  text,
  `solution_courte`       varchar(255),
  PRIMARY KEY (`questionID`)
);


CREATE TABLE `avancement` (
  `userID`       int(11) NOT NULL,
  `questionID`   int(11) NOT NULL,
  `etat`	     int DEFAULT 1,
  PRIMARY KEY (`userID`, `questionID`)
);

CREATE TABLE `avancement_prog` (
  `userID`                int(11) NOT NULL,
  `questionID`            int(11) NOT NULL,
  `lang_derniere_reponse` int,
  PRIMARY KEY (`userID`, `questionID`)
);

CREATE TABLE `reponse_sys` (
  `userID`       int(11) NOT NULL,
  `questionID`   int(11) NOT NULL,
  `conteneur`    varchar(64),
  `reponse`      varchar(255),
  PRIMARY KEY (`userID`, `questionID`)
);

CREATE TABLE `reponse_prog` (
  `userID`       int(11) NOT NULL,
  `questionID`   int(11) NOT NULL,
  `lang`         int NOT NULL,
  `code`	     text,
  PRIMARY KEY (`userID`, `questionID`, `lang`)
  );

/*Admin*/


GRANT ALL PRIVILEGES ON quiz.* TO quiz@'%';

