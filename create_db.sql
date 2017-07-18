USE quiz;

DROP TABLE IF EXISTS avancement;
DROP TABLE IF EXISTS serie;
DROP TABLE IF EXISTS theme;
DROP TABLE IF EXISTS question;
DROP TABLE IF EXISTS users;


CREATE TABLE `users` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `courriel`    varchar(255) NOT NULL,
  `actif`   int NOT NULL DEFAULT 1,

  PRIMARY KEY (`userID`)
);

CREATE TABLE `theme` (
  `themeID`     int(11) NOT NULL AUTO_INCREMENT,
  `lang`	int,
  `nom`         varchar(255) NOT NULL,
  `titre`         varchar(255) NOT NULL,
  `description` text,
  PRIMARY KEY (`themeID`)
);

CREATE TABLE `serie` (
  `serieID`     int(11) NOT NULL AUTO_INCREMENT,
  `themeID`	int(11) NOT NULL,
  `numero`	int NOT NULL,
  `nom`         varchar(255) NOT NULL,
  `titre`       varchar(255) NOT NULL,
  `url`		varchar(255),
  `description` text,
  PRIMARY KEY (`serieID`)
);

CREATE TABLE `question` (
  `questionID`	int(11) NOT NULL AUTO_INCREMENT,
  `serieID`	int(11) NOT NULL,
  `description`	text,
  `numero`	int NOT NULL,
  `nom`         varchar(255) NOT NULL,
  `titre`       varchar(255) NOT NULL,
  `lang`	int,
  `setup`	text,
  `enonce`	text,
  `pre_exec`	text,
  `pre_code`	text,
  `code`	text,
  `post_code`	text,
  `reponse`	text,
  `params`	varchar(255),
  `stdin`	varchar(255),
  `points`	int DEFAULT 0,
  PRIMARY KEY (`questionID`)
);

CREATE TABLE `avancement` (
  `userID`     int(11) NOT NULL,
  `questionID`   int(11) NOT NULL,
  `avancement`	 int DEFAULT 0,
  PRIMARY KEY (`userID`, `questionID`)
);

