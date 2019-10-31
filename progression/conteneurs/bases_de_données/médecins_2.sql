-- MySQL dump 10.17  Distrib 10.3.14-MariaDB, for Linux (x86_64)
--
-- Host: 172.17.0.2    Database: médecins
-- ------------------------------------------------------
-- Server version	10.3.10-MariaDB-1:10.3.10+maria~bionic

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `médecin`
--
DROP DATABASE IF EXISTS médecins_patients;
CREATE DATABASE médecins_patients;
USE médecins_patients;

DROP TABLE IF EXISTS `médecin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `médecin` (
  `no_license` char(7) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `no_téléphone` char(10) DEFAULT NULL,
  PRIMARY KEY (`no_license`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `médecin`
--

LOCK TABLES `médecin` WRITE;
/*!40000 ALTER TABLE `médecin` DISABLE KEYS */;
INSERT INTO `médecin` VALUES ('6463215','Adalthus Aubé','5145264259'),('756484','Adhémar Gendron','5146892618'),('4632112','Cérélise Ranger','5144571465'),('7988468','Génor Larivière','5145191821'),('1379913','Georgéus Guy','5148985764'),('326554','Laurietta Delâge','5145757762'),('2391648','Madelgarde Thibault','5141244331'),('1665312','Médérée Prince','5143436649'),('8565335','Noémie Magnan','5144532330'),('9315684','Xaviel Magnan','5147216447');
/*!40000 ALTER TABLE `médecin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patient`
--

DROP TABLE IF EXISTS `patient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patient` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) DEFAULT NULL,
  `no_téléphone` char(10) DEFAULT NULL,
  `nom_médecin` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=191 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patient`
--

LOCK TABLES `patient` WRITE;
/*!40000 ALTER TABLE `patient` DISABLE KEYS */;
INSERT INTO `patient` VALUES (1,'Romule Collard','5147574711','Madelgarde Thibault'),(2,'Elorien Lauzon','5143409203','Adhémar Gendron'),(3,'Eudélinia Lévesque','5142099660',NULL),(4,'Ezidor Ahmed','5148048470','Médérée Prince'),(5,'Delphine Babin','5146165594','Xaviel Magnan'),(6,'Francisca Vermette','5145571450','Médérée Prince'),(7,'Dolose Foisy','5148249358','Noémie Magnan'),(8,'Exélime Lecours','5145643555','Génor Larivière'),(9,'Amazélis Giguère','5142358591',NULL),(10,'Aldus Monette','5142640067','Noémie Magnan'),(11,'Amaury Blondin','5145013452','Cérélise Ranger'),(12,'Vernon Lavoie','5147147062','Madelgarde Thibault'),(13,'Alexandrine Davis','5141806067','Laurietta Delâge'),(14,'Exélime Crevier','5144255815','Xaviel Magnan'),(15,'Célanie Desrochers','5145860874','Adalthus Aubé'),(16,'Marceau Brunet','5146536930','Adhémar Gendron'),(17,'Herzélie Jutras','5145102028','Médérée Prince'),(18,'Judélia Lafrenière','5144788240','Génor Larivière'),(19,'Onas Ferland','5147524007','Adhémar Gendron'),(20,'Xélie Quévillon','5144366429','Xaviel Magnan'),(21,'Rosalinie Pearson','5147037901','Georgéus Guy'),(22,'Vénérance Carle','5143201330',NULL),(23,'Martha Morais','5142670725',NULL),(24,'Elibert François','5142638527',NULL),(25,'Gératrice Hénault','5144069350','Madelgarde Thibault'),(26,'Phalienna Malenfant','5142431171','Xaviel Magnan'),(27,'Godive Ferron','5147725583','Médérée Prince'),(28,'Théophanie Parenteau','5143556626','Laurietta Delâge'),(29,'Auzélienne  Dupré','5142384159','Madelgarde Thibault'),(30,'Palménas Desjardins','5149028697','Cérélise Ranger'),(31,'Ozinthe Desbiens','5141324343','Madelgarde Thibault'),(32,'Hermentis Gingras','5145091180','Adhémar Gendron'),(33,'Euclias Viau','5142593986',NULL),(34,'Fulbert Bourdon','5145474165','Adalthus Aubé'),(35,'Amérilda Léonard','5149588869','Noémie Magnan'),(36,'Casémire Blackburn','5143744077','Noémie Magnan'),(37,'Exzémir Joyal','5146620442',NULL),(38,'Godelise Rémillard','5142981096','Médérée Prince'),(39,'Vénérentienne Nguyen','5142821929','Noémie Magnan'),(40,'Phélime Aubé','5144055247',NULL),(41,'Axilia Joly','5141810451',NULL),(42,'Frézer Joyal','5144664292','Xaviel Magnan'),(43,'Suppliant Tétreault','5147890108','Adhémar Gendron'),(44,'Clodius Garand','5146568777','Madelgarde Thibault'),(45,'Juvénilda Viau','5148062451','Génor Larivière'),(46,'Alcédore Baribeau','5141717040','Génor Larivière'),(47,'Doria Duchesneau','5149953400','Adhémar Gendron'),(48,'Eddée Boutet','5147949217','Génor Larivière'),(49,'Onilienne Dugas','5148774774','Adalthus Aubé'),(50,'Uvia Labonté','5141137333','Médérée Prince'),(51,'Mandola Deshaies','5144917896','Médérée Prince'),(52,'Séduline Le','5142288597','Noémie Magnan'),(53,'Elsildée Lamarre','5144467075',NULL),(54,'Vitalienne Delisle','5145469584','Adhémar Gendron'),(55,'Lionil Bilodeau','5143946698','Adalthus Aubé'),(56,'Laurancinie Marceau','5142213629','Georgéus Guy'),(57,'Aldinias Garneau','5147005830','Madelgarde Thibault'),(58,'Attilina Godbout','5149499373','Georgéus Guy'),(59,'Néris Boisvert','5147590491','Médérée Prince'),(60,'Héméline Lewis','5148343224',NULL),(61,'Lirius Gouin','5148944651','Madelgarde Thibault'),(62,'Livida Bergeron','5149693583','Laurietta Delâge'),(63,'Valorie Campbell','5142745075','Georgéus Guy'),(64,'Audonie Adams','5141311291','Adhémar Gendron'),(65,'Léotice Bouchard','5146099011','Xaviel Magnan'),(66,'Théorium Dulude','5147672294','Adalthus Aubé'),(67,'Caïus Cantin','5141175549',NULL),(68,'Elilia Houle','5148416419','Médérée Prince'),(69,'Résilda Davis','5141888785',NULL),(70,'Lectaire Dumouchel','5149750222','Adalthus Aubé'),(71,'Cléomain Poudrier','5146418094','Génor Larivière'),(72,'Victurnien Kaur','5141728691','Xaviel Magnan'),(73,'Rainarde Major','5146055842','Noémie Magnan'),(74,'Edoïldée Vanier','5146204250','Adhémar Gendron'),(75,'Philodéline  Lafontaine','5142853724','Médérée Prince'),(76,'Adolise Lafontaine','5143433651','Adalthus Aubé'),(77,'Floraymond Duguay','5147495971','Médérée Prince'),(78,'Odilienne Denis','5148290014','Adalthus Aubé'),(79,'Léodille Pichette','5148962161','Cérélise Ranger'),(80,'Olancia Aubé','5149940766','Laurietta Delâge'),(81,'Gloriana Hénault','5143928458','Noémie Magnan'),(82,'Caribert Rochon','5146486660',NULL),(83,'Alcéus Bossé','5141759040','Adhémar Gendron'),(84,'Philiment Pronovost','5146001886','Noémie Magnan'),(85,'Edibert Durocher','5145843423','Génor Larivière'),(86,'Léondor Bouchard','5141211459',NULL),(87,'Virgile Latour','5145193694','Laurietta Delâge'),(88,'Rhéanne Thomas','5143445204','Adhémar Gendron'),(89,'Exilio Déziel','5149422716','Georgéus Guy'),(90,'Barbant Lamy','5149000193',NULL),(91,'Exodia Déziel','5146732820','Georgéus Guy'),(92,'Erzéas Paquette','5145552411','Xaviel Magnan'),(93,'Fridélienne St-Louis','5146452485','Noémie Magnan'),(94,'Avéri Robichaud','5145605195','Adalthus Aubé'),(95,'Alziona Comtois','5147557409',NULL),(96,'Daurette Mitchell','5142082574','Cérélise Ranger'),(97,'Frédérien McKenzie','5144407306','Cérélise Ranger'),(98,'Péranne Turbide','5145788813','Cérélise Ranger'),(99,'Pasquier Walker','5145722148','Noémie Magnan'),(100,'Sévéria Boileau','5141244302','Xaviel Magnan'),(101,'Sirias Amyot','5145721734','Madelgarde Thibault'),(102,'Josime Haché','5145986877',NULL),(103,'Harves Fraser','5142769184','Cérélise Ranger'),(104,'Youville Giasson','5143663067','Adalthus Aubé'),(105,'Philias Béland','5148896672','Génor Larivière'),(106,'Gérilda Bergevin','5145716387','Xaviel Magnan'),(107,'Enymie Bourbonnais','5149669695','Adhémar Gendron'),(108,'Enéma Phaneuf','5143421539',NULL),(109,'Louilda Sénéchal','5144765277','Cérélise Ranger'),(110,'Auriand Bourassa','5143561769','Cérélise Ranger'),(111,'Audienne Khan','5142401904','Adalthus Aubé'),(112,'Odénie Ross','5149101989',NULL),(113,'Daquin Boisclair','5141637573','Xaviel Magnan'),(114,'Précille Papineau','5146437450',NULL),(115,'Déry Blackburn','5148385644','Laurietta Delâge'),(116,'Legros Lévy','5143726987','Génor Larivière'),(117,'Vérilibe Damours','5141255778','Génor Larivière'),(118,'Elsida Faucher','5142875709','Adalthus Aubé'),(119,'Stella Soulières','5149500098','Georgéus Guy'),(120,'Elmorine Sauvé','5142206703','Génor Larivière'),(121,'Odger Genest','5148088773',NULL),(122,'Dormélina Groulx','5146045982','Noémie Magnan'),(123,'Apollinde Boulet','5144852481',NULL),(124,'Proxèze Leblond','5145013350',NULL),(125,'Eguerrand Rémillard','5149398198','Laurietta Delâge'),(126,'Dauphinée René','5144173163','Laurietta Delâge'),(127,'Fabeau Gaulin','5149337887','Médérée Prince'),(128,'Ozélia Ouellet','5146392170','Génor Larivière'),(129,'Félixianne Banville','5142836081','Noémie Magnan'),(130,'Honorense Marcil','5142781673','Laurietta Delâge'),(131,'Lascinias Davis','5144289011','Noémie Magnan'),(132,'Césarien Baillargeon','5143100039',NULL),(133,'Vilmonde Germain','5141522050',NULL),(134,'Odelbert Pinette','5146087914','Médérée Prince'),(135,'Kérildée Routhier','5146984530','Georgéus Guy'),(136,'Riella Migneault','5146658911','Cérélise Ranger'),(137,'Cléphase Mailhot','5142340969','Cérélise Ranger'),(138,'Délérance Clermont','5148394774','Noémie Magnan'),(139,'Apolidore Richard','5147173191','Madelgarde Thibault'),(140,'Nélidor Benoît','5149570524','Noémie Magnan'),(141,'Olévinie Tran','5147444158','Génor Larivière'),(142,'Elzérien Quesnel','5147398110','Xaviel Magnan'),(143,'Richaume Giroux','5144658078','Xaviel Magnan'),(144,'Mésidas Auclair','5148873838','Adhémar Gendron'),(145,'Martinus Bénard','5142617182',NULL),(146,'Onéda Jomphe','5143131063',NULL),(147,'Almiria Laporte','5146692658','Adalthus Aubé'),(148,'Claricinia Lehoux','5145181214','Cérélise Ranger'),(149,'Perside Angers','5144716985','Adalthus Aubé'),(150,'Dassylva Langlois','5146930172',NULL),(151,'Dorianna Mitchell','5141611019','Adhémar Gendron'),(152,'Donacias Laberge','5143931244',NULL),(153,'Cérias Hénault','5144823164','Génor Larivière'),(154,'Alphidore Rochette','5142322091','Médérée Prince'),(155,'Philine Busque','5144918739','Adalthus Aubé'),(156,'Enide Lacelle','5147627425','Adhémar Gendron'),(157,'Ryna Ménard','5144492021','Laurietta Delâge'),(158,'Herculain Foisy','5147355610','Xaviel Magnan'),(159,'Exarine Dufresne','5144413100','Médérée Prince'),(160,'Orcini Plourde','5147776446','Noémie Magnan'),(161,'Edilinia Lacelle','5146754046','Génor Larivière'),(162,'Choiseul Prévost','5149329781','Madelgarde Thibault'),(163,'Alménaïde Guindon','5147497880','Madelgarde Thibault'),(164,'Vimont Leblanc','5148388948',NULL),(165,'Wilhelmine St-Onge','5149451101','Adhémar Gendron'),(166,'Céphyse Blais','5143199774','Noémie Magnan'),(167,'Onéphire Martinez','5144312232','Laurietta Delâge'),(168,'Uleric Laporte','5141961839','Adalthus Aubé'),(169,'Isiode April','5144650280','Adalthus Aubé'),(170,'Minvil Létourneau','5147365882','Xaviel Magnan'),(171,'Adéleine Diotte','5143989686',NULL),(172,'Evalina Loranger','5145628562','Madelgarde Thibault'),(173,'Myrette Gariépy','5146173754','Laurietta Delâge'),(174,'Térentien Tremblay','5143983086','Madelgarde Thibault'),(175,'Eliodor Morais','5149171944','Adhémar Gendron'),(176,'Aspasie Dorval','5146132688','Madelgarde Thibault'),(177,'Eugélie Dupont','5142036496',NULL),(178,'Euphorisine Mailloux','5148451085',NULL),(179,'Urbinia Malo','5148368160','Adhémar Gendron'),(180,'Aurile Gill','5146487548','Cérélise Ranger'),(181,'Honorus Bélisle','5146222151','Noémie Magnan'),(182,'Lioze Bélisle','5141648111','Adalthus Aubé'),(183,'Majole Brodeur','5146240768',NULL),(184,'Ambrose Beaupré','5147370622','Noémie Magnan'),(185,'Mélénie Couture','5148130809','Adalthus Aubé'),(186,'Elésyne Ladouceur','5148542178','Génor Larivière'),(187,'Clophidis Clermont','5148318465',NULL),(188,'Armentia Brault','5145965792',NULL),(189,'Silvien Beaudoin','5143762456','Cérélise Ranger'),(190,'Saladine Levac','5148692683',NULL);
/*!40000 ALTER TABLE `patient` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-04-15 12:23:30

