-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: antrian_ku
-- ------------------------------------------------------
-- Server version	8.0.45

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
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(10) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Pendaftaran','A',1,'2026-05-22 02:21:26'),(7,'Pengaduan','AD',1,'2026-05-22 22:34:03');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `counters`
--

DROP TABLE IF EXISTS `counters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `counters` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `counters`
--

LOCK TABLES `counters` WRITE;
/*!40000 ALTER TABLE `counters` DISABLE KEYS */;
INSERT INTO `counters` VALUES (1,'Loket 1','1','2026-05-22 02:21:26'),(2,'Loket 2','2','2026-05-22 02:21:26'),(3,'Loket 3','3','2026-05-22 02:59:44'),(4,'Loket 4','D','2026-05-22 22:42:29'),(5,'Loket 5','E','2026-05-22 22:42:40');
/*!40000 ALTER TABLE `counters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `queues`
--

DROP TABLE IF EXISTS `queues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `queues` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `queue_number` int NOT NULL,
  `queue_code` varchar(20) NOT NULL,
  `status` enum('waiting','called','skipped','completed') DEFAULT 'waiting',
  `counter_id` int DEFAULT NULL,
  `called_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `queues_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `queues`
--

LOCK TABLES `queues` WRITE;
/*!40000 ALTER TABLE `queues` DISABLE KEYS */;
INSERT INTO `queues` VALUES (15,1,1,'A001','called',1,'2026-05-22 22:59:58',NULL,'2026-05-22 22:59:39'),(16,1,2,'A002','called',1,'2026-05-22 23:02:45',NULL,'2026-05-22 22:59:42'),(17,1,3,'A003','called',2,'2026-05-22 23:01:00',NULL,'2026-05-22 22:59:44'),(18,7,1,'AD001','called',3,'2026-05-22 23:01:24',NULL,'2026-05-22 22:59:46'),(19,7,2,'AD002','called',1,'2026-05-22 23:10:55',NULL,'2026-05-22 22:59:49'),(20,1,4,'A004','called',1,'2026-05-22 23:12:08',NULL,'2026-05-22 23:10:16'),(21,1,5,'A005','called',2,'2026-05-22 23:13:24',NULL,'2026-05-22 23:10:19'),(22,1,6,'A006','called',2,'2026-05-22 23:15:32',NULL,'2026-05-22 23:13:40'),(23,1,7,'A007','called',1,'2026-05-22 23:20:20',NULL,'2026-05-22 23:13:44'),(24,7,3,'AD003','called',1,'2026-05-22 23:20:47',NULL,'2026-05-22 23:20:36'),(25,7,4,'AD004','called',1,'2026-05-22 23:27:10',NULL,'2026-05-22 23:20:39'),(26,1,8,'A008','skipped',1,'2026-05-22 23:26:26',NULL,'2026-05-22 23:25:50'),(27,1,9,'A009','called',1,'2026-05-22 23:31:35',NULL,'2026-05-22 23:25:53'),(28,7,5,'AD005','waiting',NULL,NULL,NULL,'2026-05-22 23:25:56'),(29,7,6,'AD006','waiting',NULL,NULL,NULL,'2026-05-22 23:25:59');
/*!40000 ALTER TABLE `queues` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `key` varchar(100) NOT NULL,
  `value` text,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES ('app_name','SISTEM ANTRIAN'),('logo_path','');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-23  0:02:23
