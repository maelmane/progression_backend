DROP DATABASE quiz;

CREATE DATABASE quiz;

USE quiz;

CREATE TABLE `members` (
  `memberID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `active` varchar(255) NOT NULL,
  `resetToken` varchar(255) DEFAULT NULL,
  `resetComplete` varchar(3) DEFAULT 'No',
  PRIMARY KEY (`memberID`)
) ;

CREATE TABLE `question` (
  `questionID` int(11) NOT NULL,
  `serie`  int,
  `numero` int,
  `points` int NOT NULL
  PRIMARY KEY (`quizID`)
);

CREATE TABLE `avancement` (
  `memberID` int(11) NOT NULL,
  `questionID`   int(11) NOT NULL,
  PRIMARY KEY (`memberID`, `questionID`)
);
