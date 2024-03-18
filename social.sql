-- MySQL dump 10.13  Distrib 8.0.36, for Linux (x86_64)
--
-- Host: localhost    Database: social
-- ------------------------------------------------------
-- Server version	8.0.36-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `flagged`
--

DROP TABLE IF EXISTS `flagged`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flagged` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `cid` int NOT NULL,
  `flaggedby` tinytext NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `postid` int NOT NULL,
  `chatid` int NOT NULL,
  `hide` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flagged`
--

LOCK TABLES `flagged` WRITE;
/*!40000 ALTER TABLE `flagged` DISABLE KEYS */;
/*!40000 ALTER TABLE `flagged` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `friends`
--

DROP TABLE IF EXISTS `friends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `friends` (
  `id` int NOT NULL AUTO_INCREMENT,
  `blk` int DEFAULT NULL,
  `uid` int NOT NULL,
  `fid` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `friends`
--

LOCK TABLES `friends` WRITE;
/*!40000 ALTER TABLE `friends` DISABLE KEYS */;
/*!40000 ALTER TABLE `friends` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messenger`
--

DROP TABLE IF EXISTS `messenger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messenger` (
  `id` int NOT NULL,
  `cid` int NOT NULL,
  `fid` int NOT NULL,
  `message` tinytext NOT NULL,
  `toid` int NOT NULL,
  `uid` int NOT NULL,
  `readit` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messenger`
--

LOCK TABLES `messenger` WRITE;
/*!40000 ALTER TABLE `messenger` DISABLE KEYS */;
/*!40000 ALTER TABLE `messenger` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profile` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT 'User',
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `accounttype` varchar(255) NOT NULL DEFAULT 'Member',
  `link` varchar(255) NOT NULL DEFAULT 'Social',
  `bio` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL DEFAULT 'Internet',
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `joined` varchar(255) NOT NULL,
  `hash` varchar(256) NOT NULL,
  `activity` datetime(6) DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(6),
  `photo` varchar(255) NOT NULL,
  `active` int NOT NULL,
  `verified` int NOT NULL,
  `headerfilter` varchar(255) DEFAULT NULL,
  `photofilter` varchar(255) DEFAULT NULL,
  `header` varchar(255) DEFAULT NULL,
  `background` varchar(255) DEFAULT NULL,
  `bodycolor` varchar(255) DEFAULT NULL,
  `textcolor` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile`
--

LOCK TABLES `profile` WRITE;
/*!40000 ALTER TABLE `profile` DISABLE KEYS */;
INSERT INTO `profile` VALUES (1,'User','test','$2y$10$lkwKhCrq1dNpbSkLoyeQ5.8iYS2/BCUspxxUCnw3OdfsKZg1f7KOK','Member','Social','','Internet','info@flaneurette.nl','','February 22, 2024','01bd5acdfba54477f829c515c108f17b71d3a29d','2024-02-22 23:26:38.946982','images/profile/smile.png',1,0,'filter:brightness(124%)contrast(158%);',NULL,NULL,NULL,NULL,NULL),(2,'User','flaneurette','$2y$10$Lpu47V18AVvR25qvlGGfcuW0BiFV6Qz4dkit4npISf/YObE0h.fu6','Member','Social','','Internet','flaneurette@protonmail.com','','February 22, 2024','555d2cefa6a6cb3b5272f866ac96d8bdbdb6a079','2024-03-08 15:26:24.300620','images/profile/smile.png',1,0,'',NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shares`
--

DROP TABLE IF EXISTS `shares`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shares` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pid` int NOT NULL,
  `uid` int NOT NULL,
  `sid` int NOT NULL,
  `stat` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shares`
--

LOCK TABLES `shares` WRITE;
/*!40000 ALTER TABLE `shares` DISABLE KEYS */;
/*!40000 ALTER TABLE `shares` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stats`
--

DROP TABLE IF EXISTS `stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pid` int NOT NULL,
  `uid` int NOT NULL,
  `views` int NOT NULL,
  `shares` int NOT NULL,
  `starred` int NOT NULL,
  `likes` int NOT NULL,
  `activity` datetime(6) DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stats`
--

LOCK TABLES `stats` WRITE;
/*!40000 ALTER TABLE `stats` DISABLE KEYS */;
/*!40000 ALTER TABLE `stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timeline`
--

DROP TABLE IF EXISTS `timeline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `timeline` (
  `id` int NOT NULL AUTO_INCREMENT,
  `readit` int DEFAULT NULL,
  `toid` int DEFAULT NULL,
  `tid` int DEFAULT NULL,
  `uid` int NOT NULL,
  `cid` int NOT NULL,
  `post` tinytext NOT NULL,
  `created` int DEFAULT NULL,
  `likes` int NOT NULL,
  `shares` int NOT NULL,
  `starred` int NOT NULL,
  `comments` int NOT NULL,
  `media` varchar(255) NOT NULL,
  `edits` int NOT NULL,
  `mixedmedia` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timeline`
--

LOCK TABLES `timeline` WRITE;
/*!40000 ALTER TABLE `timeline` DISABLE KEYS */;
INSERT INTO `timeline` VALUES (1,NULL,NULL,NULL,1,0,'Test',1708643032,0,0,0,0,'',0,''),(2,NULL,NULL,NULL,1,0,'Hello how are you doing?',1708643653,0,0,0,0,'',0,''),(3,NULL,NULL,NULL,2,0,'This is a test',1708644840,0,0,0,0,'',0,''),(4,NULL,NULL,NULL,2,0,'Hello world',1708644854,0,0,0,0,'',0,''),(5,NULL,NULL,NULL,2,0,'What a beautiful day!',1708644862,0,0,0,0,'',0,''),(6,NULL,NULL,NULL,2,0,'This is a new social media platform!',1708644876,0,0,0,0,'',0,'');
/*!40000 ALTER TABLE `timeline` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-03-18 15:56:11
