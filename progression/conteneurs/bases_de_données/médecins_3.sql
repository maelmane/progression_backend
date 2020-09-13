-- MySQL dump 10.16  Distrib 10.1.26-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: 172.17.0.7    Database: médecins_patients
-- ------------------------------------------------------
-- Server version	10.3.14-MariaDB-1:10.3.14+maria~bionic

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

DROP DATABASE IF EXISTS médecins_patients;
CREATE DATABASE médecins_patients;
USE médecins_patients;

--
-- Table structure for table `médecin`
--

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
INSERT INTO `médecin` VALUES ('1379913','Georgéus Guy','5148985764'),('1665312','Médérée Prince','5143436649'),('2391648','Madelgarde Thibault','5141244331'),('326554','Laurietta Delâge','5145757762'),('4632112','Cérélise Ranger','5144571465'),('6463215','Adalthus Aubé','5145264259'),('756484','Adhémar Gendron','5146892618'),('7988468','Génor Larivière','5145191821'),('8565335','Noémie Magnan','5144532330'),('9315684','Xaviel Magnan','5147216447');
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
  `médecin_no_license` char(7) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=191 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patient`
--

LOCK TABLES `patient` WRITE;
/*!40000 ALTER TABLE `patient` DISABLE KEYS */;
INSERT INTO `patient` VALUES (1,'Romule Collard','5147574711','2391648'),(2,'Elorien Lauzon','5143409203','756484'),(3,'Eudélinia Lévesque','5142099660',NULL),(4,'Ezidor Ahmed','5148048470','1665312'),(5,'Delphine Babin','5146165594','9315684'),(6,'Francisca Vermette','5145571450','1665312'),(7,'Dolose Foisy','5148249358','8565335'),(8,'Exélime Lecours','5145643555','7988468'),(9,'Amazélis Giguère','5142358591',NULL),(10,'Aldus Monette','5142640067','8565335'),(11,'Amaury Blondin','5145013452','4632112'),(12,'Vernon Lavoie','5147147062','2391648'),(13,'Alexandrine Davis','5141806067','326554'),(14,'Exélime Crevier','5144255815','9315684'),(15,'Célanie Desrochers','5145860874','6463215'),(16,'Marceau Brunet','5146536930','756484'),(17,'Herzélie Jutras','5145102028','1665312'),(18,'Judélia Lafrenière','5144788240','7988468'),(19,'Onas Ferland','5147524007','756484'),(20,'Xélie Quévillon','5144366429','9315684'),(21,'Rosalinie Pearson','5147037901','1379913'),(22,'Vénérance Carle','5143201330',NULL),(23,'Martha Morais','5142670725',NULL),(24,'Elibert François','5142638527',NULL),(25,'Gératrice Hénault','5144069350','2391648'),(26,'Phalienna Malenfant','5142431171','9315684'),(27,'Godive Ferron','5147725583','1665312'),(28,'Théophanie Parenteau','5143556626','326554'),(29,'Auzélienne  Dupré','5142384159','2391648'),(30,'Palménas Desjardins','5149028697','4632112'),(31,'Ozinthe Desbiens','5141324343','2391648'),(32,'Hermentis Gingras','5145091180','756484'),(33,'Euclias Viau','5142593986',NULL),(34,'Fulbert Bourdon','5145474165','6463215'),(35,'Amérilda Léonard','5149588869','8565335'),(36,'Casémire Blackburn','5143744077','8565335'),(37,'Exzémir Joyal','5146620442',NULL),(38,'Godelise Rémillard','5142981096','1665312'),(39,'Vénérentienne Nguyen','5142821929','8565335'),(40,'Phélime Aubé','5144055247',NULL),(41,'Axilia Joly','5141810451',NULL),(42,'Frézer Joyal','5144664292','9315684'),(43,'Suppliant Tétreault','5147890108','756484'),(44,'Clodius Garand','5146568777','2391648'),(45,'Juvénilda Viau','5148062451','7988468'),(46,'Alcédore Baribeau','5141717040','7988468'),(47,'Doria Duchesneau','5149953400','756484'),(48,'Eddée Boutet','5147949217','7988468'),(49,'Onilienne Dugas','5148774774','6463215'),(50,'Uvia Labonté','5141137333','1665312'),(51,'Mandola Deshaies','5144917896','1665312'),(52,'Séduline Le','5142288597','8565335'),(53,'Elsildée Lamarre','5144467075',NULL),(54,'Vitalienne Delisle','5145469584','756484'),(55,'Lionil Bilodeau','5143946698','6463215'),(56,'Laurancinie Marceau','5142213629','1379913'),(57,'Aldinias Garneau','5147005830','2391648'),(58,'Attilina Godbout','5149499373','1379913'),(59,'Néris Boisvert','5147590491','1665312'),(60,'Héméline Lewis','5148343224',NULL),(61,'Lirius Gouin','5148944651','2391648'),(62,'Livida Bergeron','5149693583','326554'),(63,'Valorie Campbell','5142745075','1379913'),(64,'Audonie Adams','5141311291','756484'),(65,'Léotice Bouchard','5146099011','9315684'),(66,'Théorium Dulude','5147672294','6463215'),(67,'Caïus Cantin','5141175549',NULL),(68,'Elilia Houle','5148416419','1665312'),(69,'Résilda Davis','5141888785',NULL),(70,'Lectaire Dumouchel','5149750222','6463215'),(71,'Cléomain Poudrier','5146418094','7988468'),(72,'Victurnien Kaur','5141728691','9315684'),(73,'Rainarde Major','5146055842','8565335'),(74,'Edoïldée Vanier','5146204250','756484'),(75,'Philodéline  Lafontaine','5142853724','1665312'),(76,'Adolise Lafontaine','5143433651','6463215'),(77,'Floraymond Duguay','5147495971','1665312'),(78,'Odilienne Denis','5148290014','6463215'),(79,'Léodille Pichette','5148962161','4632112'),(80,'Olancia Aubé','5149940766','326554'),(81,'Gloriana Hénault','5143928458','8565335'),(82,'Caribert Rochon','5146486660',NULL),(83,'Alcéus Bossé','5141759040','756484'),(84,'Philiment Pronovost','5146001886','8565335'),(85,'Edibert Durocher','5145843423','7988468'),(86,'Léondor Bouchard','5141211459',NULL),(87,'Virgile Latour','5145193694','326554'),(88,'Rhéanne Thomas','5143445204','756484'),(89,'Exilio Déziel','5149422716','1379913'),(90,'Barbant Lamy','5149000193',NULL),(91,'Exodia Déziel','5146732820','1379913'),(92,'Erzéas Paquette','5145552411','9315684'),(93,'Fridélienne St-Louis','5146452485','8565335'),(94,'Avéri Robichaud','5145605195','6463215'),(95,'Alziona Comtois','5147557409',NULL),(96,'Daurette Mitchell','5142082574','4632112'),(97,'Frédérien McKenzie','5144407306','4632112'),(98,'Péranne Turbide','5145788813','4632112'),(99,'Pasquier Walker','5145722148','8565335'),(100,'Sévéria Boileau','5141244302','9315684'),(101,'Sirias Amyot','5145721734','2391648'),(102,'Josime Haché','5145986877',NULL),(103,'Harves Fraser','5142769184','4632112'),(104,'Youville Giasson','5143663067','6463215'),(105,'Philias Béland','5148896672','7988468'),(106,'Gérilda Bergevin','5145716387','9315684'),(107,'Enymie Bourbonnais','5149669695','756484'),(108,'Enéma Phaneuf','5143421539',NULL),(109,'Louilda Sénéchal','5144765277','4632112'),(110,'Auriand Bourassa','5143561769','4632112'),(111,'Audienne Khan','5142401904','6463215'),(112,'Odénie Ross','5149101989',NULL),(113,'Daquin Boisclair','5141637573','9315684'),(114,'Précille Papineau','5146437450',NULL),(115,'Déry Blackburn','5148385644','326554'),(116,'Legros Lévy','5143726987','7988468'),(117,'Vérilibe Damours','5141255778','7988468'),(118,'Elsida Faucher','5142875709','6463215'),(119,'Stella Soulières','5149500098','1379913'),(120,'Elmorine Sauvé','5142206703','7988468'),(121,'Odger Genest','5148088773',NULL),(122,'Dormélina Groulx','5146045982','8565335'),(123,'Apollinde Boulet','5144852481',NULL),(124,'Proxèze Leblond','5145013350',NULL),(125,'Eguerrand Rémillard','5149398198','326554'),(126,'Dauphinée René','5144173163','326554'),(127,'Fabeau Gaulin','5149337887','1665312'),(128,'Ozélia Ouellet','5146392170','7988468'),(129,'Félixianne Banville','5142836081','8565335'),(130,'Honorense Marcil','5142781673','326554'),(131,'Lascinias Davis','5144289011','8565335'),(132,'Césarien Baillargeon','5143100039',NULL),(133,'Vilmonde Germain','5141522050',NULL),(134,'Odelbert Pinette','5146087914','1665312'),(135,'Kérildée Routhier','5146984530','1379913'),(136,'Riella Migneault','5146658911','4632112'),(137,'Cléphase Mailhot','5142340969','4632112'),(138,'Délérance Clermont','5148394774','8565335'),(139,'Apolidore Richard','5147173191','2391648'),(140,'Nélidor Benoît','5149570524','8565335'),(141,'Olévinie Tran','5147444158','7988468'),(142,'Elzérien Quesnel','5147398110','9315684'),(143,'Richaume Giroux','5144658078','9315684'),(144,'Mésidas Auclair','5148873838','756484'),(145,'Martinus Bénard','5142617182',NULL),(146,'Onéda Jomphe','5143131063',NULL),(147,'Almiria Laporte','5146692658','6463215'),(148,'Claricinia Lehoux','5145181214','4632112'),(149,'Perside Angers','5144716985','6463215'),(150,'Dassylva Langlois','5146930172',NULL),(151,'Dorianna Mitchell','5141611019','756484'),(152,'Donacias Laberge','5143931244',NULL),(153,'Cérias Hénault','5144823164','7988468'),(154,'Alphidore Rochette','5142322091','1665312'),(155,'Philine Busque','5144918739','6463215'),(156,'Enide Lacelle','5147627425','756484'),(157,'Ryna Ménard','5144492021','326554'),(158,'Herculain Foisy','5147355610','9315684'),(159,'Exarine Dufresne','5144413100','1665312'),(160,'Orcini Plourde','5147776446','8565335'),(161,'Edilinia Lacelle','5146754046','7988468'),(162,'Choiseul Prévost','5149329781','2391648'),(163,'Alménaïde Guindon','5147497880','2391648'),(164,'Vimont Leblanc','5148388948',NULL),(165,'Wilhelmine St-Onge','5149451101','756484'),(166,'Céphyse Blais','5143199774','8565335'),(167,'Onéphire Martinez','5144312232','326554'),(168,'Uleric Laporte','5141961839','6463215'),(169,'Isiode April','5144650280','6463215'),(170,'Minvil Létourneau','5147365882','9315684'),(171,'Adéleine Diotte','5143989686',NULL),(172,'Evalina Loranger','5145628562','2391648'),(173,'Myrette Gariépy','5146173754','326554'),(174,'Térentien Tremblay','5143983086','2391648'),(175,'Eliodor Morais','5149171944','756484'),(176,'Aspasie Dorval','5146132688','2391648'),(177,'Eugélie Dupont','5142036496',NULL),(178,'Euphorisine Mailloux','5148451085',NULL),(179,'Urbinia Malo','5148368160','756484'),(180,'Aurile Gill','5146487548','4632112'),(181,'Honorus Bélisle','5146222151','8565335'),(182,'Lioze Bélisle','5141648111','6463215'),(183,'Majole Brodeur','5146240768',NULL),(184,'Ambrose Beaupré','5147370622','8565335'),(185,'Mélénie Couture','5148130809','6463215'),(186,'Elésyne Ladouceur','5148542178','7988468'),(187,'Clophidis Clermont','5148318465',NULL),(188,'Armentia Brault','5145965792',NULL),(189,'Silvien Beaudoin','5143762456','4632112'),(190,'Saladine Levac','5148692683',NULL);
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

-- Dump completed on 2019-04-15 13:51:37
