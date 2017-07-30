DROP TABLE IF EXISTS avancement;
DROP TABLE IF EXISTS serie;
DROP TABLE IF EXISTS theme;
DROP TABLE IF EXISTS question;
DROP TABLE IF EXISTS question_prog;
DROP TABLE IF EXISTS question_systeme;
DROP TABLE IF EXISTS users;


CREATE TABLE `users` (
  `userID`   int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `courriel` varchar(255),
  `actif`    int NOT NULL DEFAULT 1,

  PRIMARY KEY (`userID`)
);

CREATE TABLE `theme` (
  `themeID`     int(11) NOT NULL AUTO_INCREMENT,
  `lang`	int,
  `titre`       varchar(255) NOT NULL,
  `ordre`       int,
  `description` text,
  PRIMARY KEY (`themeID`)
);

CREATE TABLE `serie` (
  `serieID`     int(11) NOT NULL AUTO_INCREMENT,
  `themeID`	int(11) NOT NULL,
  `numero`	int NOT NULL,
  `titre`       varchar(255) NOT NULL,
  `description` text,
  PRIMARY KEY (`serieID`)
);

CREATE TABLE `question` (
  `questionID`	int(11) NOT NULL AUTO_INCREMENT,
  `type`        int NOT NULL,
  `serieID`	int(11) NOT NULL,
  `titre`       varchar(255) NOT NULL,
  `description`	text,
  `numero`	int NOT NULL,
  `enonce`	text,
  `points`	int DEFAULT 0,
  PRIMARY KEY (`questionID`)
);

CREATE TABLE `question_prog` (
  `questionID`  int(11) NOT NULL,
  `lang`	int,
  `setup`	text,
  `pre_exec`	text,
  `pre_code`	text,
  `incode`	text,
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
  `etat`	 int DEFAULT 0,
  `reponse`	 text,
  PRIMARY KEY (`userID`, `questionID`)
);

