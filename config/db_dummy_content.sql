-- MariaDB dump 10.19  Distrib 10.6.5-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: default
-- ------------------------------------------------------
-- Server version	10.6.5-MariaDB-1:10.6.5+maria~focal

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
-- Table structure for table `v6__accounting_document__types`
--

DROP TABLE IF EXISTS `v6__accounting_document__types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__accounting_document__types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(48) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__accounting_document__types`
--

LOCK TABLES `v6__accounting_document__types` WRITE;
/*!40000 ALTER TABLE `v6__accounting_document__types` DISABLE KEYS */;
INSERT INTO `v6__accounting_document__types` VALUES (1,'Proforma'),(2,'Dispatch'),(3,'Invoice'),(4,'Recurrence');
/*!40000 ALTER TABLE `v6__accounting_document__types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__accounting_documents`
--

DROP TABLE IF EXISTS `v6__accounting_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__accounting_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `modified_by_user_id` int(11) DEFAULT NULL,
  `ordinal_num_in_year` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `title` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `is_archived` tinyint(1) NOT NULL,
  `note` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_E89D44C1C54C8C93` (`type_id`),
  KEY `IDX_E89D44C119EB6921` (`client_id`),
  KEY `IDX_E89D44C1727ACA70` (`parent_id`),
  KEY `IDX_E89D44C17D182D95` (`created_by_user_id`),
  KEY `IDX_E89D44C1DD5BE62E` (`modified_by_user_id`),
  CONSTRAINT `FK_E89D44C119EB6921` FOREIGN KEY (`client_id`) REFERENCES `v6__clients` (`id`),
  CONSTRAINT `FK_E89D44C1727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `v6__accounting_documents` (`id`),
  CONSTRAINT `FK_E89D44C17D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_E89D44C1C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `v6__accounting_document__types` (`id`),
  CONSTRAINT `FK_E89D44C1DD5BE62E` FOREIGN KEY (`modified_by_user_id`) REFERENCES `v6__users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__accounting_documents`
--

LOCK TABLES `v6__accounting_documents` WRITE;
/*!40000 ALTER TABLE `v6__accounting_documents` DISABLE KEYS */;
INSERT INTO `v6__accounting_documents` VALUES (1,1,3,NULL,1,NULL,1,'2022-12-20 08:21:42','Predračun broj jedan',0,'','2022-12-20 08:21:42','1970-01-01 00:00:00');
/*!40000 ALTER TABLE `v6__accounting_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__accounting_documents__articles`
--

DROP TABLE IF EXISTS `v6__accounting_documents__articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__accounting_documents__articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accounting_document_id` int(11) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL,
  `pieces` decimal(11,0) NOT NULL,
  `price` decimal(11,4) NOT NULL,
  `discount` decimal(11,2) NOT NULL,
  `tax` decimal(11,2) NOT NULL,
  `weight` decimal(11,0) NOT NULL,
  `note` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4C48ABD1BF80C2E7` (`accounting_document_id`),
  KEY `IDX_4C48ABD17294869C` (`article_id`),
  CONSTRAINT `FK_4C48ABD17294869C` FOREIGN KEY (`article_id`) REFERENCES `v6__articles` (`id`),
  CONSTRAINT `FK_4C48ABD1BF80C2E7` FOREIGN KEY (`accounting_document_id`) REFERENCES `v6__accounting_documents` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__accounting_documents__articles`
--

LOCK TABLES `v6__accounting_documents__articles` WRITE;
/*!40000 ALTER TABLE `v6__accounting_documents__articles` DISABLE KEYS */;
INSERT INTO `v6__accounting_documents__articles` VALUES (1,1,1,1,123.4500,0.00,20.00,0,''),(2,1,4,10,12345.6700,0.00,20.00,0,'');
/*!40000 ALTER TABLE `v6__accounting_documents__articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__accounting_documents__articles__properties`
--

DROP TABLE IF EXISTS `v6__accounting_documents__articles__properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__accounting_documents__articles__properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accounting_document_article_id` int(11) DEFAULT NULL,
  `property_id` int(11) DEFAULT NULL,
  `quantity` decimal(11,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1ABB808986F1E756` (`accounting_document_article_id`),
  KEY `IDX_1ABB8089549213EC` (`property_id`),
  CONSTRAINT `FK_1ABB8089549213EC` FOREIGN KEY (`property_id`) REFERENCES `v6__properties` (`id`),
  CONSTRAINT `FK_1ABB808986F1E756` FOREIGN KEY (`accounting_document_article_id`) REFERENCES `v6__accounting_documents__articles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__accounting_documents__articles__properties`
--

LOCK TABLES `v6__accounting_documents__articles__properties` WRITE;
/*!40000 ALTER TABLE `v6__accounting_documents__articles__properties` DISABLE KEYS */;
/*!40000 ALTER TABLE `v6__accounting_documents__articles__properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__accounting_documents__payments`
--

DROP TABLE IF EXISTS `v6__accounting_documents__payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__accounting_documents__payments` (
  `accountingdocument_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  PRIMARY KEY (`accountingdocument_id`,`payment_id`),
  KEY `IDX_9647018BC321EF81` (`accountingdocument_id`),
  KEY `IDX_9647018B4C3A3BB` (`payment_id`),
  CONSTRAINT `FK_9647018B4C3A3BB` FOREIGN KEY (`payment_id`) REFERENCES `v6__payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_9647018BC321EF81` FOREIGN KEY (`accountingdocument_id`) REFERENCES `v6__accounting_documents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__accounting_documents__payments`
--

LOCK TABLES `v6__accounting_documents__payments` WRITE;
/*!40000 ALTER TABLE `v6__accounting_documents__payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `v6__accounting_documents__payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__article__groups`
--

DROP TABLE IF EXISTS `v6__article__groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__article__groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(48) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__article__groups`
--

LOCK TABLES `v6__article__groups` WRITE;
/*!40000 ALTER TABLE `v6__article__groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `v6__article__groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__articles`
--

DROP TABLE IF EXISTS `v6__articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `modified_by_user_id` int(11) DEFAULT NULL,
  `name` varchar(96) COLLATE utf8mb3_unicode_ci NOT NULL,
  `weight` decimal(11,0) NOT NULL,
  `min_calc_measure` decimal(11,2) NOT NULL,
  `price` decimal(11,4) NOT NULL,
  `note` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_4F76DE47FE54D947` (`group_id`),
  KEY `IDX_4F76DE47F8BD700D` (`unit_id`),
  KEY `IDX_4F76DE477D182D95` (`created_by_user_id`),
  KEY `IDX_4F76DE47DD5BE62E` (`modified_by_user_id`),
  CONSTRAINT `FK_4F76DE477D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_4F76DE47DD5BE62E` FOREIGN KEY (`modified_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_4F76DE47F8BD700D` FOREIGN KEY (`unit_id`) REFERENCES `v6__units` (`id`),
  CONSTRAINT `FK_4F76DE47FE54D947` FOREIGN KEY (`group_id`) REFERENCES `v6__article__groups` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__articles`
--

LOCK TABLES `v6__articles` WRITE;
/*!40000 ALTER TABLE `v6__articles` DISABLE KEYS */;
INSERT INTO `v6__articles` VALUES (1,NULL,1,1,1,'Proizvod 01',0,1.00,123.4500,'','2022-12-20 08:13:51','2022-12-20 08:13:58'),(2,NULL,1,1,1,'Proizvod za testiranje 02',0,1.00,23.8800,'','2022-12-20 08:14:49','2022-12-20 08:14:58'),(3,NULL,1,1,1,'Treći proizvod za testiranje',0,1.00,1234.5600,'','2022-12-20 08:16:43','2022-12-20 08:16:48'),(4,NULL,6,1,NULL,'Testiramo četvrti proizvod sa malo dužim nazivom',0,1.00,12345.6700,'','2022-12-20 08:19:48','1970-01-01 00:00:00');
/*!40000 ALTER TABLE `v6__articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__articles__properties`
--

DROP TABLE IF EXISTS `v6__articles__properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__articles__properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) DEFAULT NULL,
  `property_id` int(11) DEFAULT NULL,
  `min_size` decimal(11,0) NOT NULL,
  `max_size` decimal(11,0) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_599BD78A7294869C` (`article_id`),
  KEY `IDX_599BD78A549213EC` (`property_id`),
  CONSTRAINT `FK_599BD78A549213EC` FOREIGN KEY (`property_id`) REFERENCES `v6__properties` (`id`),
  CONSTRAINT `FK_599BD78A7294869C` FOREIGN KEY (`article_id`) REFERENCES `v6__articles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__articles__properties`
--

LOCK TABLES `v6__articles__properties` WRITE;
/*!40000 ALTER TABLE `v6__articles__properties` DISABLE KEYS */;
/*!40000 ALTER TABLE `v6__articles__properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__cities`
--

DROP TABLE IF EXISTS `v6__cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by_user_id` int(11) DEFAULT NULL,
  `modified_by_user_id` int(11) DEFAULT NULL,
  `name` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_74F24C0D7D182D95` (`created_by_user_id`),
  KEY `IDX_74F24C0DDD5BE62E` (`modified_by_user_id`),
  CONSTRAINT `FK_74F24C0D7D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_74F24C0DDD5BE62E` FOREIGN KEY (`modified_by_user_id`) REFERENCES `v6__users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__cities`
--

LOCK TABLES `v6__cities` WRITE;
/*!40000 ALTER TABLE `v6__cities` DISABLE KEYS */;
INSERT INTO `v6__cities` VALUES (1,1,NULL,'Bačka Palanka','2022-12-17 10:00:31','0001-01-01 00:00:00'),(2,1,NULL,'Novi Sad','2022-12-17 10:00:38','0001-01-01 00:00:00'),(3,1,NULL,'Beograd','2022-12-17 10:00:47','0001-01-01 00:00:00'),(4,1,NULL,'Smederevska Palanka','2022-12-17 10:05:34','0001-01-01 00:00:00'),(5,1,NULL,'Bosanski Petrovac','2022-12-17 10:08:31','0001-01-01 00:00:00');
/*!40000 ALTER TABLE `v6__cities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__client__types`
--

DROP TABLE IF EXISTS `v6__client__types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__client__types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(48) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__client__types`
--

LOCK TABLES `v6__client__types` WRITE;
/*!40000 ALTER TABLE `v6__client__types` DISABLE KEYS */;
INSERT INTO `v6__client__types` VALUES (1,'Fizičko lice'),(2,'Pravno lice');
/*!40000 ALTER TABLE `v6__client__types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__clients`
--

DROP TABLE IF EXISTS `v6__clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `street_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `modified_by_user_id` int(11) DEFAULT NULL,
  `name` varchar(96) COLLATE utf8mb3_unicode_ci NOT NULL,
  `name_note` varchar(128) COLLATE utf8mb3_unicode_ci NOT NULL,
  `lb` varchar(13) COLLATE utf8mb3_unicode_ci NOT NULL,
  `is_supplier` tinyint(1) NOT NULL,
  `home_number` varchar(8) COLLATE utf8mb3_unicode_ci NOT NULL,
  `address_note` varchar(128) COLLATE utf8mb3_unicode_ci NOT NULL,
  `note` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_A4B445E4C54C8C93` (`type_id`),
  KEY `IDX_A4B445E4F92F3E70` (`country_id`),
  KEY `IDX_A4B445E48BAC62AF` (`city_id`),
  KEY `IDX_A4B445E487CF8EB` (`street_id`),
  KEY `IDX_A4B445E47D182D95` (`created_by_user_id`),
  KEY `IDX_A4B445E4DD5BE62E` (`modified_by_user_id`),
  CONSTRAINT `FK_A4B445E47D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_A4B445E487CF8EB` FOREIGN KEY (`street_id`) REFERENCES `v6__streets` (`id`),
  CONSTRAINT `FK_A4B445E48BAC62AF` FOREIGN KEY (`city_id`) REFERENCES `v6__cities` (`id`),
  CONSTRAINT `FK_A4B445E4C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `v6__client__types` (`id`),
  CONSTRAINT `FK_A4B445E4DD5BE62E` FOREIGN KEY (`modified_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_A4B445E4F92F3E70` FOREIGN KEY (`country_id`) REFERENCES `v6__countries` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__clients`
--

LOCK TABLES `v6__clients` WRITE;
/*!40000 ALTER TABLE `v6__clients` DISABLE KEYS */;
INSERT INTO `v6__clients` VALUES (1,1,1,1,2,1,NULL,'Marko Markovic','','',0,'5','','','2022-12-17 10:02:27','1970-01-01 00:00:00'),(2,1,1,2,1,1,1,'Petar Petrović','','',0,'2a','','','2022-12-17 10:02:57','2022-12-17 10:03:56'),(3,1,1,NULL,NULL,1,1,'Janko Janković','','',0,'','','','2022-12-17 10:03:12','2022-12-17 10:03:39'),(4,1,1,4,3,1,1,'Predrag Predragović','','',0,'123A','','','2022-12-17 10:06:10','2022-12-17 10:06:38'),(5,1,3,5,4,1,1,'Miodrag Gavrankapetanović','','',0,'123C','','','2022-12-17 10:07:31','2022-12-17 10:10:19'),(6,2,1,1,1,1,NULL,'D-OFFICE doo','','',1,'1','','','2022-12-17 13:24:58','1970-01-01 00:00:00'),(7,2,1,4,3,1,1,'SAMOSTALNA TRGOVINSKA RADNJA MARKET MITAR MITROVIC  PR, SMEDEREVSKA PALANKA','','',1,'321','','','2022-12-17 13:44:02','2022-12-17 13:44:46');
/*!40000 ALTER TABLE `v6__clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__clients__contacts`
--

DROP TABLE IF EXISTS `v6__clients__contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__clients__contacts` (
  `client_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  PRIMARY KEY (`client_id`,`contact_id`),
  KEY `IDX_1B48356D19EB6921` (`client_id`),
  KEY `IDX_1B48356DE7A1254A` (`contact_id`),
  CONSTRAINT `FK_1B48356D19EB6921` FOREIGN KEY (`client_id`) REFERENCES `v6__clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_1B48356DE7A1254A` FOREIGN KEY (`contact_id`) REFERENCES `v6__contacts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__clients__contacts`
--

LOCK TABLES `v6__clients__contacts` WRITE;
/*!40000 ALTER TABLE `v6__clients__contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `v6__clients__contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__contact__types`
--

DROP TABLE IF EXISTS `v6__contact__types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__contact__types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(48) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__contact__types`
--

LOCK TABLES `v6__contact__types` WRITE;
/*!40000 ALTER TABLE `v6__contact__types` DISABLE KEYS */;
INSERT INTO `v6__contact__types` VALUES (1,'tel'),(2,'mob-tel'),(3,'fax'),(4,'e-mail'),(5,'web');
/*!40000 ALTER TABLE `v6__contact__types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__contacts`
--

DROP TABLE IF EXISTS `v6__contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `modified_by_user_id` int(11) DEFAULT NULL,
  `body` varchar(48) COLLATE utf8mb3_unicode_ci NOT NULL,
  `note` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_C3EBFA5CC54C8C93` (`type_id`),
  KEY `IDX_C3EBFA5C7D182D95` (`created_by_user_id`),
  KEY `IDX_C3EBFA5CDD5BE62E` (`modified_by_user_id`),
  CONSTRAINT `FK_C3EBFA5C7D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_C3EBFA5CC54C8C93` FOREIGN KEY (`type_id`) REFERENCES `v6__contact__types` (`id`),
  CONSTRAINT `FK_C3EBFA5CDD5BE62E` FOREIGN KEY (`modified_by_user_id`) REFERENCES `v6__users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__contacts`
--

LOCK TABLES `v6__contacts` WRITE;
/*!40000 ALTER TABLE `v6__contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `v6__contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__countries`
--

DROP TABLE IF EXISTS `v6__countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by_user_id` int(11) DEFAULT NULL,
  `modified_by_user_id` int(11) DEFAULT NULL,
  `name` varchar(24) COLLATE utf8mb3_unicode_ci NOT NULL,
  `abbr` varchar(3) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_F6477D1B7D182D95` (`created_by_user_id`),
  KEY `IDX_F6477D1BDD5BE62E` (`modified_by_user_id`),
  CONSTRAINT `FK_F6477D1B7D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_F6477D1BDD5BE62E` FOREIGN KEY (`modified_by_user_id`) REFERENCES `v6__users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__countries`
--

LOCK TABLES `v6__countries` WRITE;
/*!40000 ALTER TABLE `v6__countries` DISABLE KEYS */;
INSERT INTO `v6__countries` VALUES (1,1,NULL,'Srbija','','2022-12-17 09:59:52','0000-01-01 00:00:00'),(2,1,NULL,'Hrvatska','','2022-12-17 10:00:00','0000-01-01 00:00:00'),(3,1,NULL,'Bosna i Hercegovina','','2022-12-17 10:00:09','0000-01-01 00:00:00');
/*!40000 ALTER TABLE `v6__countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__cutting_sheets`
--

DROP TABLE IF EXISTS `v6__cutting_sheets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__cutting_sheets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `modified_by_user_id` int(11) DEFAULT NULL,
  `ordinal_num_in_year` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_CE14197C19EB6921` (`client_id`),
  KEY `IDX_CE14197C7D182D95` (`created_by_user_id`),
  KEY `IDX_CE14197CDD5BE62E` (`modified_by_user_id`),
  CONSTRAINT `FK_CE14197C19EB6921` FOREIGN KEY (`client_id`) REFERENCES `v6__clients` (`id`),
  CONSTRAINT `FK_CE14197C7D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_CE14197CDD5BE62E` FOREIGN KEY (`modified_by_user_id`) REFERENCES `v6__users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__cutting_sheets`
--

LOCK TABLES `v6__cutting_sheets` WRITE;
/*!40000 ALTER TABLE `v6__cutting_sheets` DISABLE KEYS */;
/*!40000 ALTER TABLE `v6__cutting_sheets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__cutting_sheets__article`
--

DROP TABLE IF EXISTS `v6__cutting_sheets__article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__cutting_sheets__article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cutting_sheet_id` int(11) DEFAULT NULL,
  `fence_model_id` int(11) DEFAULT NULL,
  `picket_width` decimal(11,0) NOT NULL,
  `width` decimal(11,0) NOT NULL,
  `height` decimal(11,0) NOT NULL,
  `mid_height` decimal(11,0) NOT NULL,
  `space` decimal(11,0) NOT NULL,
  `number_of_fields` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_95A277A387DE9F8E` (`cutting_sheet_id`),
  KEY `IDX_95A277A3385FB227` (`fence_model_id`),
  CONSTRAINT `FK_95A277A3385FB227` FOREIGN KEY (`fence_model_id`) REFERENCES `v6__fence__models` (`id`),
  CONSTRAINT `FK_95A277A387DE9F8E` FOREIGN KEY (`cutting_sheet_id`) REFERENCES `v6__cutting_sheets` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__cutting_sheets__article`
--

LOCK TABLES `v6__cutting_sheets__article` WRITE;
/*!40000 ALTER TABLE `v6__cutting_sheets__article` DISABLE KEYS */;
/*!40000 ALTER TABLE `v6__cutting_sheets__article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__employees`
--

DROP TABLE IF EXISTS `v6__employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(48) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__employees`
--

LOCK TABLES `v6__employees` WRITE;
/*!40000 ALTER TABLE `v6__employees` DISABLE KEYS */;
/*!40000 ALTER TABLE `v6__employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__fence__models`
--

DROP TABLE IF EXISTS `v6__fence__models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__fence__models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(48) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__fence__models`
--

LOCK TABLES `v6__fence__models` WRITE;
/*!40000 ALTER TABLE `v6__fence__models` DISABLE KEYS */;
/*!40000 ALTER TABLE `v6__fence__models` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__materials`
--

DROP TABLE IF EXISTS `v6__materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `modified_by_user_id` int(11) DEFAULT NULL,
  `name` varchar(96) COLLATE utf8mb3_unicode_ci NOT NULL,
  `weight` decimal(11,0) NOT NULL,
  `min_calc_measure` decimal(11,2) NOT NULL,
  `price` decimal(11,4) NOT NULL,
  `note` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_30368003F8BD700D` (`unit_id`),
  KEY `IDX_303680037D182D95` (`created_by_user_id`),
  KEY `IDX_30368003DD5BE62E` (`modified_by_user_id`),
  CONSTRAINT `FK_303680037D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_30368003DD5BE62E` FOREIGN KEY (`modified_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_30368003F8BD700D` FOREIGN KEY (`unit_id`) REFERENCES `v6__units` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__materials`
--

LOCK TABLES `v6__materials` WRITE;
/*!40000 ALTER TABLE `v6__materials` DISABLE KEYS */;
INSERT INTO `v6__materials` VALUES (1,1,1,NULL,'Hemijska olovka',0,0.00,0.5000,'','2022-12-18 08:48:28','0000-01-01 00:00:00'),(2,1,1,NULL,'Papir za stampac A4 (500kom)',0,0.00,4.2000,'','2022-12-18 08:49:16','0000-01-01 00:00:00'),(3,1,1,1,'Kafa zrno 200g',200,0.00,1.5000,'','2022-12-18 08:50:18','2022-12-18 10:50:46'),(4,1,1,NULL,'Domestos 750ml',0,0.00,2.8000,'','2022-12-18 08:51:37','0000-01-01 00:00:00'),(5,1,1,1,'Šećer 1kg',1000,0.00,0.8500,'','2022-12-18 09:43:32','2022-12-18 11:43:40'),(6,1,1,1,'Mleko Moja Kravica 1.5lit',0,0.00,1.1300,'','2022-12-18 09:46:24','2022-12-18 11:46:41');
/*!40000 ALTER TABLE `v6__materials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__materials__properties`
--

DROP TABLE IF EXISTS `v6__materials__properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__materials__properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `material_id` int(11) DEFAULT NULL,
  `property_id` int(11) DEFAULT NULL,
  `min_size` decimal(11,0) NOT NULL,
  `max_size` decimal(11,0) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F196A683E308AC6F` (`material_id`),
  KEY `IDX_F196A683549213EC` (`property_id`),
  CONSTRAINT `FK_F196A683549213EC` FOREIGN KEY (`property_id`) REFERENCES `v6__properties` (`id`),
  CONSTRAINT `FK_F196A683E308AC6F` FOREIGN KEY (`material_id`) REFERENCES `v6__materials` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__materials__properties`
--

LOCK TABLES `v6__materials__properties` WRITE;
/*!40000 ALTER TABLE `v6__materials__properties` DISABLE KEYS */;
/*!40000 ALTER TABLE `v6__materials__properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__materials__suppliers`
--

DROP TABLE IF EXISTS `v6__materials__suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__materials__suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `material_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `modified_by_user_id` int(11) DEFAULT NULL,
  `note` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `price` decimal(11,4) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_25FE6D1CE308AC6F` (`material_id`),
  KEY `IDX_25FE6D1C2ADD6D8C` (`supplier_id`),
  KEY `IDX_25FE6D1C7D182D95` (`created_by_user_id`),
  KEY `IDX_25FE6D1CDD5BE62E` (`modified_by_user_id`),
  CONSTRAINT `FK_25FE6D1C2ADD6D8C` FOREIGN KEY (`supplier_id`) REFERENCES `v6__clients` (`id`),
  CONSTRAINT `FK_25FE6D1C7D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_25FE6D1CDD5BE62E` FOREIGN KEY (`modified_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_25FE6D1CE308AC6F` FOREIGN KEY (`material_id`) REFERENCES `v6__materials` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__materials__suppliers`
--

LOCK TABLES `v6__materials__suppliers` WRITE;
/*!40000 ALTER TABLE `v6__materials__suppliers` DISABLE KEYS */;
INSERT INTO `v6__materials__suppliers` VALUES (1,1,6,1,1,'',0.5000,'2022-12-18 08:48:34','2022-12-18 08:49:35'),(2,2,6,1,NULL,'',0.0000,'2022-12-18 08:49:25','1070-01-01 00:00:00'),(3,3,7,1,1,'',1.5000,'2022-12-18 08:50:23','2022-12-18 08:50:26'),(4,4,7,1,NULL,'',0.0000,'2022-12-18 08:51:42','1070-01-01 00:00:00'),(5,5,7,1,1,'',0.8500,'2022-12-18 09:43:45','2022-12-18 09:43:51'),(6,6,7,1,1,'',1.1300,'2022-12-18 09:46:30','2022-12-18 09:46:34');
/*!40000 ALTER TABLE `v6__materials__suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__orders`
--

DROP TABLE IF EXISTS `v6__orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `modified_by_user_id` int(11) DEFAULT NULL,
  `ordinal_num_in_year` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `title` varchar(196) COLLATE utf8mb3_unicode_ci NOT NULL,
  `is_archived` tinyint(1) NOT NULL,
  `status` int(11) NOT NULL,
  `note` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_488000882ADD6D8C` (`supplier_id`),
  KEY `IDX_488000887D182D95` (`created_by_user_id`),
  KEY `IDX_48800088DD5BE62E` (`modified_by_user_id`),
  CONSTRAINT `FK_488000882ADD6D8C` FOREIGN KEY (`supplier_id`) REFERENCES `v6__clients` (`id`),
  CONSTRAINT `FK_488000887D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_48800088DD5BE62E` FOREIGN KEY (`modified_by_user_id`) REFERENCES `v6__users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__orders`
--

LOCK TABLES `v6__orders` WRITE;
/*!40000 ALTER TABLE `v6__orders` DISABLE KEYS */;
INSERT INTO `v6__orders` VALUES (1,7,1,NULL,1,'2022-12-18 08:55:56','Hemija za odrzavanje higijene',0,0,'','2022-12-18 08:55:56','0000-01-01 00:00:00'),(2,6,1,NULL,2,'2022-12-18 08:59:12','Kancelarijski materijal',0,0,'','2022-12-18 08:59:12','0000-01-01 00:00:00'),(3,7,1,NULL,3,'2022-12-18 09:39:35','Kuhinjeske potrepstine i reprezentacija',1,2,'','2022-12-18 09:39:35','0000-01-01 00:00:00');
/*!40000 ALTER TABLE `v6__orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__orders__materials`
--

DROP TABLE IF EXISTS `v6__orders__materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__orders__materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `material_id` int(11) DEFAULT NULL,
  `pieces` decimal(11,0) NOT NULL,
  `price` decimal(11,4) NOT NULL,
  `discount` decimal(11,2) NOT NULL,
  `tax` decimal(11,2) NOT NULL,
  `weight` decimal(11,0) NOT NULL,
  `note` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E4CF4A1D8D9F6D38` (`order_id`),
  KEY `IDX_E4CF4A1DE308AC6F` (`material_id`),
  CONSTRAINT `FK_E4CF4A1D8D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `v6__orders` (`id`),
  CONSTRAINT `FK_E4CF4A1DE308AC6F` FOREIGN KEY (`material_id`) REFERENCES `v6__materials` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__orders__materials`
--

LOCK TABLES `v6__orders__materials` WRITE;
/*!40000 ALTER TABLE `v6__orders__materials` DISABLE KEYS */;
INSERT INTO `v6__orders__materials` VALUES (1,1,4,1,2.8000,0.00,20.00,0,''),(2,2,1,10,0.5000,0.00,20.00,0,''),(3,2,2,5,4.2000,0.00,20.00,0,''),(4,3,3,2,1.5000,0.00,20.00,200,''),(5,3,5,1,0.8500,0.00,20.00,1000,''),(6,3,6,1,1.1300,0.00,20.00,0,'');
/*!40000 ALTER TABLE `v6__orders__materials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__orders__materials__properties`
--

DROP TABLE IF EXISTS `v6__orders__materials__properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__orders__materials__properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_material_id` int(11) DEFAULT NULL,
  `property_id` int(11) DEFAULT NULL,
  `quantity` decimal(11,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F20B5BF3F8BAD3E9` (`order_material_id`),
  KEY `IDX_F20B5BF3549213EC` (`property_id`),
  CONSTRAINT `FK_F20B5BF3549213EC` FOREIGN KEY (`property_id`) REFERENCES `v6__properties` (`id`),
  CONSTRAINT `FK_F20B5BF3F8BAD3E9` FOREIGN KEY (`order_material_id`) REFERENCES `v6__orders__materials` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__orders__materials__properties`
--

LOCK TABLES `v6__orders__materials__properties` WRITE;
/*!40000 ALTER TABLE `v6__orders__materials__properties` DISABLE KEYS */;
/*!40000 ALTER TABLE `v6__orders__materials__properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__payment__types`
--

DROP TABLE IF EXISTS `v6__payment__types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__payment__types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(48) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__payment__types`
--

LOCK TABLES `v6__payment__types` WRITE;
/*!40000 ALTER TABLE `v6__payment__types` DISABLE KEYS */;
INSERT INTO `v6__payment__types` VALUES (1,'Avans (gotovinski)'),(2,'Avans (virmanski)'),(3,'Uplata (gotovinska)'),(4,'Uplata (virmanska)'),(5,'Početno stanje kase'),(6,'Izlaz gotovine na kraju dana (smene)'),(7,'Izlaz gotovine');
/*!40000 ALTER TABLE `v6__payment__types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__payments`
--

DROP TABLE IF EXISTS `v6__payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `amount` decimal(11,4) NOT NULL,
  `note` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_9579741DC54C8C93` (`type_id`),
  KEY `IDX_9579741D7D182D95` (`created_by_user_id`),
  CONSTRAINT `FK_9579741D7D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_9579741DC54C8C93` FOREIGN KEY (`type_id`) REFERENCES `v6__payment__types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__payments`
--

LOCK TABLES `v6__payments` WRITE;
/*!40000 ALTER TABLE `v6__payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `v6__payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__preferences`
--

DROP TABLE IF EXISTS `v6__preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kurs` decimal(11,4) NOT NULL,
  `tax` decimal(11,4) NOT NULL,
  `company_name` varchar(128) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `company_pib` varchar(13) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `company_mb` int(8) NOT NULL,
  `company_country_id` int(11) DEFAULT NULL,
  `company_city_id` int(11) DEFAULT NULL,
  `company_street_id` int(11) DEFAULT NULL,
  `company_home_number` varchar(8) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `company_bank_account_1` varchar(128) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `company_bank_account_2` varchar(128) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `local_backup_folder` varchar(128) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__preferences`
--

LOCK TABLES `v6__preferences` WRITE;
/*!40000 ALTER TABLE `v6__preferences` DISABLE KEYS */;
INSERT INTO `v6__preferences` VALUES (1,118.0000,20.0000,'D Office','123456789',234234,1,1,1,'xx','123-45-67, Banka Iks','','C:/');
/*!40000 ALTER TABLE `v6__preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__project__priorities`
--

DROP TABLE IF EXISTS `v6__project__priorities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__project__priorities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(48) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_384A62C25E237E06` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__project__priorities`
--

LOCK TABLES `v6__project__priorities` WRITE;
/*!40000 ALTER TABLE `v6__project__priorities` DISABLE KEYS */;
INSERT INTO `v6__project__priorities` VALUES (4,'Nizak'),(3,'Normalan'),(1,'Urgent'),(2,'Visok');
/*!40000 ALTER TABLE `v6__project__priorities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__project__statuses`
--

DROP TABLE IF EXISTS `v6__project__statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__project__statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(48) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_238AFA945E237E06` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__project__statuses`
--

LOCK TABLES `v6__project__statuses` WRITE;
/*!40000 ALTER TABLE `v6__project__statuses` DISABLE KEYS */;
INSERT INTO `v6__project__statuses` VALUES (1,'Aktivan'),(2,'Na čekanju'),(3,'U arhivi');
/*!40000 ALTER TABLE `v6__project__statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__project_task_statuses`
--

DROP TABLE IF EXISTS `v6__project_task_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__project_task_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(48) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__project_task_statuses`
--

LOCK TABLES `v6__project_task_statuses` WRITE;
/*!40000 ALTER TABLE `v6__project_task_statuses` DISABLE KEYS */;
INSERT INTO `v6__project_task_statuses` VALUES (1,'Za realizaciju'),(2,'U realizaciji'),(3,'Realizovani');
/*!40000 ALTER TABLE `v6__project_task_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__project_task_types`
--

DROP TABLE IF EXISTS `v6__project_task_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__project_task_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(48) COLLATE utf8mb3_unicode_ci NOT NULL,
  `class` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__project_task_types`
--

LOCK TABLES `v6__project_task_types` WRITE;
/*!40000 ALTER TABLE `v6__project_task_types` DISABLE KEYS */;
INSERT INTO `v6__project_task_types` VALUES (1,'Merenje','info'),(2,'Ponuda','warning'),(3,'Nabavka','secondary'),(4,'Proizvodnja','success'),(5,'Isporuka','isporuka'),(6,'Montaža','yellow'),(7,'Reklamacija','danger'),(8,'Popravka','popravka');
/*!40000 ALTER TABLE `v6__project_task_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__projects`
--

DROP TABLE IF EXISTS `v6__projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL,
  `priority_id` int(11) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `modified_by_user_id` int(11) DEFAULT NULL,
  `ordinal_num_in_year` int(11) NOT NULL,
  `title` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `note` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_AC385C8B19EB6921` (`client_id`),
  KEY `IDX_AC385C8B497B19F9` (`priority_id`),
  KEY `IDX_AC385C8B6BF700BD` (`status_id`),
  KEY `IDX_AC385C8B7D182D95` (`created_by_user_id`),
  KEY `IDX_AC385C8BDD5BE62E` (`modified_by_user_id`),
  CONSTRAINT `FK_AC385C8B19EB6921` FOREIGN KEY (`client_id`) REFERENCES `v6__clients` (`id`),
  CONSTRAINT `FK_AC385C8B497B19F9` FOREIGN KEY (`priority_id`) REFERENCES `v6__project__priorities` (`id`),
  CONSTRAINT `FK_AC385C8B6BF700BD` FOREIGN KEY (`status_id`) REFERENCES `v6__project__statuses` (`id`),
  CONSTRAINT `FK_AC385C8B7D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_AC385C8BDD5BE62E` FOREIGN KEY (`modified_by_user_id`) REFERENCES `v6__users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__projects`
--

LOCK TABLES `v6__projects` WRITE;
/*!40000 ALTER TABLE `v6__projects` DISABLE KEYS */;
INSERT INTO `v6__projects` VALUES (1,3,3,1,1,NULL,1,'Projekat za testiranje','','2022-12-19 08:18:58','1970-01-01 00:00:00'),(2,5,3,1,1,NULL,2,'Projekat za testiranje No2','','2022-12-19 23:32:21','1970-01-01 00:00:00');
/*!40000 ALTER TABLE `v6__projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__projects__accounting_documents`
--

DROP TABLE IF EXISTS `v6__projects__accounting_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__projects__accounting_documents` (
  `project_id` int(11) NOT NULL,
  `accountingdocument_id` int(11) NOT NULL,
  PRIMARY KEY (`project_id`,`accountingdocument_id`),
  KEY `IDX_6611E5ED166D1F9C` (`project_id`),
  KEY `IDX_6611E5EDC321EF81` (`accountingdocument_id`),
  CONSTRAINT `FK_6611E5ED166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `v6__projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_6611E5EDC321EF81` FOREIGN KEY (`accountingdocument_id`) REFERENCES `v6__accounting_documents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__projects__accounting_documents`
--

LOCK TABLES `v6__projects__accounting_documents` WRITE;
/*!40000 ALTER TABLE `v6__projects__accounting_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `v6__projects__accounting_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__projects__notes`
--

DROP TABLE IF EXISTS `v6__projects__notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__projects__notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `modified_by_user_id` int(11) DEFAULT NULL,
  `note` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_15A18EED166D1F9C` (`project_id`),
  KEY `IDX_15A18EED7D182D95` (`created_by_user_id`),
  KEY `IDX_15A18EEDDD5BE62E` (`modified_by_user_id`),
  CONSTRAINT `FK_15A18EED166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `v6__projects` (`id`),
  CONSTRAINT `FK_15A18EED7D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_15A18EEDDD5BE62E` FOREIGN KEY (`modified_by_user_id`) REFERENCES `v6__users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__projects__notes`
--

LOCK TABLES `v6__projects__notes` WRITE;
/*!40000 ALTER TABLE `v6__projects__notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `v6__projects__notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__projects__orders`
--

DROP TABLE IF EXISTS `v6__projects__orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__projects__orders` (
  `project_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  PRIMARY KEY (`project_id`,`order_id`),
  KEY `IDX_DF8E1608166D1F9C` (`project_id`),
  KEY `IDX_DF8E16088D9F6D38` (`order_id`),
  CONSTRAINT `FK_DF8E1608166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `v6__projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_DF8E16088D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `v6__orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__projects__orders`
--

LOCK TABLES `v6__projects__orders` WRITE;
/*!40000 ALTER TABLE `v6__projects__orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `v6__projects__orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__projects__tasks`
--

DROP TABLE IF EXISTS `v6__projects__tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__projects__tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `modified_by_user_id` int(11) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `title` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `start_date` datetime NOT NULL DEFAULT current_timestamp(),
  `end_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_44E24DF6166D1F9C` (`project_id`),
  KEY `IDX_44E24DF67D182D95` (`created_by_user_id`),
  KEY `IDX_44E24DF6DD5BE62E` (`modified_by_user_id`),
  KEY `IDX_44E24DF6C54C8C93` (`type_id`),
  KEY `IDX_44E24DF66BF700BD` (`status_id`),
  KEY `IDX_44E24DF68C03F15C` (`employee_id`),
  CONSTRAINT `FK_44E24DF6166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `v6__projects` (`id`),
  CONSTRAINT `FK_44E24DF66BF700BD` FOREIGN KEY (`status_id`) REFERENCES `v6__project_task_statuses` (`id`),
  CONSTRAINT `FK_44E24DF67D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_44E24DF68C03F15C` FOREIGN KEY (`employee_id`) REFERENCES `v6__employees` (`id`),
  CONSTRAINT `FK_44E24DF6C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `v6__project_task_types` (`id`),
  CONSTRAINT `FK_44E24DF6DD5BE62E` FOREIGN KEY (`modified_by_user_id`) REFERENCES `v6__users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__projects__tasks`
--

LOCK TABLES `v6__projects__tasks` WRITE;
/*!40000 ALTER TABLE `v6__projects__tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `v6__projects__tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__projects_tasks_notes`
--

DROP TABLE IF EXISTS `v6__projects_tasks_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__projects_tasks_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_task_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `modified_by_user_id` int(11) DEFAULT NULL,
  `note` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_FC6F18EC1BA80DE3` (`project_task_id`),
  KEY `IDX_FC6F18EC7D182D95` (`created_by_user_id`),
  KEY `IDX_FC6F18ECDD5BE62E` (`modified_by_user_id`),
  CONSTRAINT `FK_FC6F18EC1BA80DE3` FOREIGN KEY (`project_task_id`) REFERENCES `v6__projects__tasks` (`id`),
  CONSTRAINT `FK_FC6F18EC7D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_FC6F18ECDD5BE62E` FOREIGN KEY (`modified_by_user_id`) REFERENCES `v6__users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__projects_tasks_notes`
--

LOCK TABLES `v6__projects_tasks_notes` WRITE;
/*!40000 ALTER TABLE `v6__projects_tasks_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `v6__projects_tasks_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__properties`
--

DROP TABLE IF EXISTS `v6__properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(48) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__properties`
--

LOCK TABLES `v6__properties` WRITE;
/*!40000 ALTER TABLE `v6__properties` DISABLE KEYS */;
INSERT INTO `v6__properties` VALUES (1,'duzina'),(2,'sirina'),(3,'visina');
/*!40000 ALTER TABLE `v6__properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__streets`
--

DROP TABLE IF EXISTS `v6__streets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__streets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by_user_id` int(11) DEFAULT NULL,
  `modified_by_user_id` int(11) DEFAULT NULL,
  `name` varchar(40) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_378A10AE7D182D95` (`created_by_user_id`),
  KEY `IDX_378A10AEDD5BE62E` (`modified_by_user_id`),
  CONSTRAINT `FK_378A10AE7D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_378A10AEDD5BE62E` FOREIGN KEY (`modified_by_user_id`) REFERENCES `v6__users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__streets`
--

LOCK TABLES `v6__streets` WRITE;
/*!40000 ALTER TABLE `v6__streets` DISABLE KEYS */;
INSERT INTO `v6__streets` VALUES (1,1,NULL,'Kralja Petra I','2022-12-17 10:00:57','0000-01-01 00:00:00'),(2,1,NULL,'Šafarikova','2022-12-17 10:01:24','0000-01-01 00:00:00'),(3,1,NULL,'Vojvode Živojina Mišića','2022-12-17 10:05:12','0000-01-01 00:00:00'),(4,1,NULL,'dr. Jovana Raškovića','2022-12-17 10:09:58','0000-01-01 00:00:00');
/*!40000 ALTER TABLE `v6__streets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__units`
--

DROP TABLE IF EXISTS `v6__units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(48) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__units`
--

LOCK TABLES `v6__units` WRITE;
/*!40000 ALTER TABLE `v6__units` DISABLE KEYS */;
INSERT INTO `v6__units` VALUES (1,'kom'),(2,'m'),(3,'m2'),(4,'par'),(5,'set'),(6,'km'),(7,'kg');
/*!40000 ALTER TABLE `v6__units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__user__roles`
--

DROP TABLE IF EXISTS `v6__user__roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__user__roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(16) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__user__roles`
--

LOCK TABLES `v6__user__roles` WRITE;
/*!40000 ALTER TABLE `v6__user__roles` DISABLE KEYS */;
INSERT INTO `v6__user__roles` VALUES (1,'SuperAdmin'),(2,'Administrator'),(3,'Službenik'),(4,'Radnik');
/*!40000 ALTER TABLE `v6__user__roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `v6__users`
--

DROP TABLE IF EXISTS `v6__users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `v6__users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `modified_by_user_id` int(11) DEFAULT NULL,
  `username` varchar(25) COLLATE utf8mb3_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_DCF68583F85E0677` (`username`),
  KEY `IDX_DCF68583D60322AC` (`role_id`),
  KEY `IDX_DCF685837D182D95` (`created_by_user_id`),
  KEY `IDX_DCF68583DD5BE62E` (`modified_by_user_id`),
  CONSTRAINT `FK_DCF685837D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `v6__users` (`id`),
  CONSTRAINT `FK_DCF68583D60322AC` FOREIGN KEY (`role_id`) REFERENCES `v6__user__roles` (`id`),
  CONSTRAINT `FK_DCF68583DD5BE62E` FOREIGN KEY (`modified_by_user_id`) REFERENCES `v6__users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `v6__users`
--

LOCK TABLES `v6__users` WRITE;
/*!40000 ALTER TABLE `v6__users` DISABLE KEYS */;
INSERT INTO `v6__users` VALUES (1,1,1,NULL,'admin','xx','2021-02-16 08:41:25','0001-01-01 00:00:00');
/*!40000 ALTER TABLE `v6__users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-12-20  8:22:37
