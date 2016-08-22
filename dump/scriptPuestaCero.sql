-- MySQL dump 10.13  Distrib 5.6.24, for Win64 (x86_64)
--
-- Host: localhost    Database: reaxiumQA
-- ------------------------------------------------------
-- Server version	5.5.49-0ubuntu0.14.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `access_options_rol`
--

DROP TABLE IF EXISTS `access_options_rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `access_options_rol` (
  `user_type_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `active_menu` int(11) NOT NULL,
  PRIMARY KEY (`menu_id`,`user_type_id`),
  KEY `menu_id_fk_idx` (`menu_id`),
  KEY `user_type_id_fk_idx` (`user_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `access_options_rol`
--

LOCK TABLES `access_options_rol` WRITE;
/*!40000 ALTER TABLE `access_options_rol` DISABLE KEYS */;
INSERT INTO `access_options_rol` VALUES (1,1,1),(2,1,0),(3,1,0),(4,1,0),(5,1,0),(6,1,0),(1,2,1),(2,2,0),(3,2,0),(4,2,0),(5,2,1),(6,2,1),(1,3,1),(2,3,0),(3,3,0),(4,3,0),(5,3,0),(6,3,1),(1,4,1),(2,4,0),(3,4,0),(4,4,0),(5,4,1),(6,4,1),(1,5,1),(2,5,0),(3,5,0),(4,5,0),(5,5,1),(6,5,1),(1,6,1),(2,6,0),(3,6,0),(4,6,0),(5,6,0),(6,6,0);
/*!40000 ALTER TABLE `access_options_rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `access_type`
--

DROP TABLE IF EXISTS `access_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `access_type` (
  `access_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `access_type_name` varchar(100) NOT NULL,
  `status_id` int(11) NOT NULL,
  PRIMARY KEY (`access_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `access_type`
--

LOCK TABLES `access_type` WRITE;
/*!40000 ALTER TABLE `access_type` DISABLE KEYS */;
INSERT INTO `access_type` VALUES (1,'User Login and Password',1),(2,'Biometric',1),(3,'RFID',1),(4,'DocumentID',1);
/*!40000 ALTER TABLE `access_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `address` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `address` varchar(1500) NOT NULL,
  `latitude` varchar(45) NOT NULL,
  `longitude` varchar(45) NOT NULL,
  PRIMARY KEY (`address_id`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `address`
--

LOCK TABLES `address` WRITE;
/*!40000 ALTER TABLE `address` DISABLE KEYS */;
/*!40000 ALTER TABLE `address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `address_relationship`
--

DROP TABLE IF EXISTS `address_relationship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `address_relationship` (
  `address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`address_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `address_relationship`
--

LOCK TABLES `address_relationship` WRITE;
/*!40000 ALTER TABLE `address_relationship` DISABLE KEYS */;
/*!40000 ALTER TABLE `address_relationship` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `applications`
--

DROP TABLE IF EXISTS `applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applications` (
  `application_id` int(11) NOT NULL AUTO_INCREMENT,
  `application_name` varchar(150) NOT NULL,
  `status_id` int(11) NOT NULL DEFAULT '1',
  `version` float NOT NULL,
  PRIMARY KEY (`application_id`),
  UNIQUE KEY `application_name_UNIQUE` (`application_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applications`
--

LOCK TABLES `applications` WRITE;
/*!40000 ALTER TABLE `applications` DISABLE KEYS */;
/*!40000 ALTER TABLE `applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `applications_relationship`
--

DROP TABLE IF EXISTS `applications_relationship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applications_relationship` (
  `application_id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  PRIMARY KEY (`application_id`,`device_id`),
  UNIQUE KEY `device_id_UNIQUE` (`device_id`),
  CONSTRAINT `app_fk` FOREIGN KEY (`application_id`) REFERENCES `applications` (`application_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `device_fk` FOREIGN KEY (`device_id`) REFERENCES `reaxium_device` (`device_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applications_relationship`
--

LOCK TABLES `applications_relationship` WRITE;
/*!40000 ALTER TABLE `applications_relationship` DISABLE KEYS */;
/*!40000 ALTER TABLE `applications_relationship` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `business`
--

DROP TABLE IF EXISTS `business`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `business` (
  `business_id` int(11) NOT NULL AUTO_INCREMENT,
  `business_name` varchar(100) NOT NULL,
  `business_id_number` varchar(100) NOT NULL,
  `address_id` int(11) DEFAULT NULL,
  `phone_number_id` int(11) DEFAULT NULL,
  `status_id` int(11) DEFAULT '1',
  PRIMARY KEY (`business_id`),
  UNIQUE KEY `business_id_number_UNIQUE` (`business_id_number`),
  KEY `address_business_fk_idx` (`address_id`),
  KEY `phone_number_business_fk_idx` (`phone_number_id`),
  KEY `status_business_fk_idx` (`status_id`),
  CONSTRAINT `address_business_fk` FOREIGN KEY (`address_id`) REFERENCES `address` (`address_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `phone_number_business_fk` FOREIGN KEY (`phone_number_id`) REFERENCES `phone_numbers` (`phone_number_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `status_business_fk` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business`
--

LOCK TABLES `business` WRITE;
/*!40000 ALTER TABLE `business` DISABLE KEYS */;
INSERT INTO `business` VALUES (1,'Reaxium Admin Business','1',NULL,NULL,1);
/*!40000 ALTER TABLE `business` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `business_relationship`
--

DROP TABLE IF EXISTS `business_relationship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `business_relationship` (
  `business_id` int(11) NOT NULL,
  `business_object_id` int(11) NOT NULL,
  PRIMARY KEY (`business_id`,`business_object_id`),
  CONSTRAINT `business_rel_id_fk` FOREIGN KEY (`business_id`) REFERENCES `business` (`business_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business_relationship`
--

LOCK TABLES `business_relationship` WRITE;
/*!40000 ALTER TABLE `business_relationship` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_relationship` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device_access_control`
--

DROP TABLE IF EXISTS `device_access_control`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device_access_control` (
  `access_device_control_id` int(11) NOT NULL AUTO_INCREMENT,
  `device_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `access_type_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`access_device_control_id`,`device_id`,`application_id`,`access_type_id`),
  KEY `access_control_full` (`device_id`,`application_id`,`access_type_id`,`status_id`),
  KEY `access_type_device_access_fk_idx` (`access_type_id`),
  KEY `app_access_controll_fk_idx` (`application_id`),
  KEY `status_access_control_fk_idx` (`status_id`),
  CONSTRAINT `access_type_device_access_fk` FOREIGN KEY (`access_type_id`) REFERENCES `access_type` (`access_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `app_access_controll_fk` FOREIGN KEY (`application_id`) REFERENCES `applications` (`application_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `device_access_control_fk` FOREIGN KEY (`device_id`) REFERENCES `reaxium_device` (`device_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `status_access_control_fk` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_access_control`
--

LOCK TABLES `device_access_control` WRITE;
/*!40000 ALTER TABLE `device_access_control` DISABLE KEYS */;
/*!40000 ALTER TABLE `device_access_control` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device_business`
--

DROP TABLE IF EXISTS `device_business`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device_business` (
  `device_id` int(11) NOT NULL DEFAULT '0',
  `business_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`device_id`,`business_id`),
  KEY `business_id_busi_fk_idx` (`business_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_business`
--

LOCK TABLES `device_business` WRITE;
/*!40000 ALTER TABLE `device_business` DISABLE KEYS */;
/*!40000 ALTER TABLE `device_business` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device_location`
--

DROP TABLE IF EXISTS `device_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device_location` (
  `device_location_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `device_id` int(11) DEFAULT NULL,
  `latitude` varchar(20) DEFAULT NULL,
  `longitude` varchar(20) DEFAULT NULL,
  `date_location` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`device_location_id`),
  KEY `device_id_device_location_rel_idx` (`device_id`),
  KEY `user_device_location_fk_idx` (`user_id`),
  CONSTRAINT `device_id_device_location_rel` FOREIGN KEY (`device_id`) REFERENCES `reaxium_device` (`device_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `user_device_location_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_location`
--

LOCK TABLES `device_location` WRITE;
/*!40000 ALTER TABLE `device_location` DISABLE KEYS */;
/*!40000 ALTER TABLE `device_location` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device_location_stakeholder`
--

DROP TABLE IF EXISTS `device_location_stakeholder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device_location_stakeholder` (
  `device_location_stakeholder_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_stakeholder_id` int(11) NOT NULL,
  `user_in_track_id` int(11) NOT NULL,
  `device_token` varchar(1500) NOT NULL,
  `device_platform` varchar(45) NOT NULL,
  `device_id` int(11) NOT NULL,
  `registry_date` datetime DEFAULT NULL,
  PRIMARY KEY (`device_location_stakeholder_id`),
  KEY `userstakeholderidlocationdevice_fk_idx` (`user_stakeholder_id`),
  KEY `userintracklocationdevice_fk_idx` (`user_in_track_id`),
  KEY `device_device_location_fk_idx` (`device_id`),
  CONSTRAINT `device_device_location_fk` FOREIGN KEY (`device_id`) REFERENCES `reaxium_device` (`device_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `userintracklocationdevice_fk` FOREIGN KEY (`user_in_track_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `userstakeholderidlocationdevice_fk` FOREIGN KEY (`user_stakeholder_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_location_stakeholder`
--

LOCK TABLES `device_location_stakeholder` WRITE;
/*!40000 ALTER TABLE `device_location_stakeholder` DISABLE KEYS */;
/*!40000 ALTER TABLE `device_location_stakeholder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device_routes`
--

DROP TABLE IF EXISTS `device_routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device_routes` (
  `id_device_routes` int(11) NOT NULL AUTO_INCREMENT,
  `id_route` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `start_date` time NOT NULL,
  `end_date` time NOT NULL,
  PRIMARY KEY (`id_device_routes`),
  KEY `id_route_fk_idx` (`id_route`),
  KEY `device_id_fk_idx` (`device_id`),
  CONSTRAINT `device_id_fk` FOREIGN KEY (`device_id`) REFERENCES `reaxium_device` (`device_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `id_route_fk` FOREIGN KEY (`id_route`) REFERENCES `routes` (`id_route`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_routes`
--

LOCK TABLES `device_routes` WRITE;
/*!40000 ALTER TABLE `device_routes` DISABLE KEYS */;
/*!40000 ALTER TABLE `device_routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_application`
--

DROP TABLE IF EXISTS `menu_application`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_application` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_menu` int(11) NOT NULL DEFAULT '1',
  `name_menu` varchar(45) NOT NULL,
  `icon_class` varchar(25) NOT NULL,
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_application`
--

LOCK TABLES `menu_application` WRITE;
/*!40000 ALTER TABLE `menu_application` DISABLE KEYS */;
INSERT INTO `menu_application` VALUES (1,1,'School Administration','fa fa-bank'),(2,1,'User Administration','fa fa-user'),(3,1,'Device Administration','fa fa-hdd-o'),(4,1,'Routes Administration','fa fa-map-signs'),(5,1,'Stops Administration','fa fa-street-view'),(6,1,'Super User Options','fa fa-gear');
/*!40000 ALTER TABLE `menu_application` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phone_numbers`
--

DROP TABLE IF EXISTS `phone_numbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phone_numbers` (
  `phone_number_id` int(11) NOT NULL AUTO_INCREMENT,
  `phone_name` varchar(45) NOT NULL,
  `phone_number` varchar(45) NOT NULL,
  PRIMARY KEY (`phone_number_id`)
) ENGINE=InnoDB AUTO_INCREMENT=301 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phone_numbers`
--

LOCK TABLES `phone_numbers` WRITE;
/*!40000 ALTER TABLE `phone_numbers` DISABLE KEYS */;
/*!40000 ALTER TABLE `phone_numbers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phone_numbers_relationship`
--

DROP TABLE IF EXISTS `phone_numbers_relationship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phone_numbers_relationship` (
  `phone_number_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`phone_number_id`,`user_id`),
  UNIQUE KEY `phone_number_id_UNIQUE` (`phone_number_id`),
  KEY `user_phone_rel_fk_idx` (`user_id`),
  CONSTRAINT `phone_number_id_rel_fk` FOREIGN KEY (`phone_number_id`) REFERENCES `phone_numbers` (`phone_number_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `user_phone_rel_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phone_numbers_relationship`
--

LOCK TABLES `phone_numbers_relationship` WRITE;
/*!40000 ALTER TABLE `phone_numbers_relationship` DISABLE KEYS */;
/*!40000 ALTER TABLE `phone_numbers_relationship` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reaxium_device`
--

DROP TABLE IF EXISTS `reaxium_device`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reaxium_device` (
  `device_id` int(11) NOT NULL AUTO_INCREMENT,
  `device_name` varchar(45) DEFAULT NULL,
  `device_description` varchar(45) DEFAULT NULL,
  `status_id` int(11) NOT NULL DEFAULT '1',
  `configured` tinyint(1) DEFAULT '0',
  `device_token` varchar(2500) DEFAULT NULL,
  `device_serial` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`device_id`),
  KEY `reaxium_device_status_fk_idx` (`status_id`),
  CONSTRAINT `reaxium_device_status_fk` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reaxium_device`
--

LOCK TABLES `reaxium_device` WRITE;
/*!40000 ALTER TABLE `reaxium_device` DISABLE KEYS */;
/*!40000 ALTER TABLE `reaxium_device` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `route_type`
--

DROP TABLE IF EXISTS `route_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `route_type` (
  `route_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `route_type_name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`route_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `route_type`
--

LOCK TABLES `route_type` WRITE;
/*!40000 ALTER TABLE `route_type` DISABLE KEYS */;
INSERT INTO `route_type` VALUES (1,'Pick Up'),(2,'Drop Off');
/*!40000 ALTER TABLE `route_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `routes`
--

DROP TABLE IF EXISTS `routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `routes` (
  `id_route` int(11) NOT NULL AUTO_INCREMENT,
  `route_number` varchar(45) NOT NULL,
  `route_name` varchar(45) NOT NULL,
  `route_address` varchar(80) NOT NULL,
  `routes_stops_count` int(11) NOT NULL DEFAULT '0',
  `status_id` int(11) DEFAULT '1',
  `route_type` int(11) DEFAULT NULL,
  `overview_polyline` varchar(2500) DEFAULT NULL,
  PRIMARY KEY (`id_route`),
  KEY `route_type_id_fk_idx` (`route_type`),
  CONSTRAINT `route_type_id_fk` FOREIGN KEY (`route_type`) REFERENCES `route_type` (`route_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routes`
--

LOCK TABLES `routes` WRITE;
/*!40000 ALTER TABLE `routes` DISABLE KEYS */;
/*!40000 ALTER TABLE `routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `routes_history`
--

DROP TABLE IF EXISTS `routes_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `routes_history` (
  `id_routes_history` int(11) NOT NULL AUTO_INCREMENT,
  `date_initial` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `device_id` int(11) DEFAULT NULL,
  `user_max` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_routes_history`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routes_history`
--

LOCK TABLES `routes_history` WRITE;
/*!40000 ALTER TABLE `routes_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `routes_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `routes_stops_relationship`
--

DROP TABLE IF EXISTS `routes_stops_relationship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `routes_stops_relationship` (
  `id_route` int(11) NOT NULL DEFAULT '0',
  `id_stop` int(11) NOT NULL DEFAULT '0',
  `order_stop` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_route`,`id_stop`),
  KEY `id_routes_fk_idx` (`id_route`),
  KEY `id_stop_fk_idx` (`id_stop`),
  CONSTRAINT `id_routes_fk` FOREIGN KEY (`id_route`) REFERENCES `routes` (`id_route`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `id_stop_fk` FOREIGN KEY (`id_stop`) REFERENCES `stops` (`id_stop`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routes_stops_relationship`
--

LOCK TABLES `routes_stops_relationship` WRITE;
/*!40000 ALTER TABLE `routes_stops_relationship` DISABLE KEYS */;
/*!40000 ALTER TABLE `routes_stops_relationship` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schoolbus`
--

DROP TABLE IF EXISTS `schoolbus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schoolbus` (
  `business_object_id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolbus_serial_code` varchar(45) NOT NULL,
  `schoolbus_name` varchar(100) DEFAULT NULL,
  `seats` int(11) NOT NULL,
  `device_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`business_object_id`,`schoolbus_serial_code`),
  UNIQUE KEY `schoolbus_serial_code_UNIQUE` (`schoolbus_serial_code`),
  KEY `status_rel_bus_fk_idx` (`status_id`),
  KEY `user_rel_bus_fk_idx` (`user_id`),
  KEY `device_rel_bus_fk_idx` (`device_id`),
  CONSTRAINT `device_rel_bus_fk` FOREIGN KEY (`device_id`) REFERENCES `reaxium_device` (`device_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `status_rel_bus_fk` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `user_rel_bus_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schoolbus`
--

LOCK TABLES `schoolbus` WRITE;
/*!40000 ALTER TABLE `schoolbus` DISABLE KEYS */;
/*!40000 ALTER TABLE `schoolbus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stakeholder_type`
--

DROP TABLE IF EXISTS `stakeholder_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stakeholder_type` (
  `stakeholder_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `stakeholder_type_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`stakeholder_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stakeholder_type`
--

LOCK TABLES `stakeholder_type` WRITE;
/*!40000 ALTER TABLE `stakeholder_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `stakeholder_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stakeholder_type_relationship`
--

DROP TABLE IF EXISTS `stakeholder_type_relationship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stakeholder_type_relationship` (
  `stakeholder_id` int(11) NOT NULL,
  `stakeholder_type_id` int(11) NOT NULL,
  PRIMARY KEY (`stakeholder_id`,`stakeholder_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stakeholder_type_relationship`
--

LOCK TABLES `stakeholder_type_relationship` WRITE;
/*!40000 ALTER TABLE `stakeholder_type_relationship` DISABLE KEYS */;
/*!40000 ALTER TABLE `stakeholder_type_relationship` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stakeholders`
--

DROP TABLE IF EXISTS `stakeholders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stakeholders` (
  `stakeholder_id` int(11) NOT NULL AUTO_INCREMENT,
  `status_id` int(11) NOT NULL DEFAULT '1',
  `android_id` varchar(1500) DEFAULT NULL,
  `ios_id` varchar(1500) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`stakeholder_id`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stakeholders`
--

LOCK TABLES `stakeholders` WRITE;
/*!40000 ALTER TABLE `stakeholders` DISABLE KEYS */;
/*!40000 ALTER TABLE `stakeholders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status`
--

DROP TABLE IF EXISTS `status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status` (
  `status_id` int(11) NOT NULL AUTO_INCREMENT,
  `status_name` varchar(100) NOT NULL,
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status`
--

LOCK TABLES `status` WRITE;
/*!40000 ALTER TABLE `status` DISABLE KEYS */;
INSERT INTO `status` VALUES (1,'ACTIVE'),(2,'INACTIVE'),(3,'DELETED');
/*!40000 ALTER TABLE `status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stops`
--

DROP TABLE IF EXISTS `stops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stops` (
  `id_stop` int(11) NOT NULL AUTO_INCREMENT,
  `stop_number` varchar(20) DEFAULT NULL,
  `stop_name` varchar(45) DEFAULT NULL,
  `stop_latitude` varchar(20) NOT NULL,
  `stop_longitude` varchar(20) NOT NULL,
  `stop_address` varchar(80) DEFAULT NULL,
  `status_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_stop`)
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stops`
--

LOCK TABLES `stops` WRITE;
/*!40000 ALTER TABLE `stops` DISABLE KEYS */;
/*!40000 ALTER TABLE `stops` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stops_users`
--

DROP TABLE IF EXISTS `stops_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stops_users` (
  `id_stops_user` int(11) NOT NULL AUTO_INCREMENT,
  `id_stop` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  PRIMARY KEY (`id_stops_user`),
  KEY `id_stop_idx` (`id_stop`),
  KEY `user_id_fk_idx` (`user_id`),
  CONSTRAINT `id_stop_fk1` FOREIGN KEY (`id_stop`) REFERENCES `stops` (`id_stop`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `user_id_fk1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stops_users`
--

LOCK TABLES `stops_users` WRITE;
/*!40000 ALTER TABLE `stops_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `stops_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sub_menu_application`
--

DROP TABLE IF EXISTS `sub_menu_application`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sub_menu_application` (
  `sub_menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `url` varchar(45) NOT NULL,
  `menu_id` int(11) NOT NULL,
  PRIMARY KEY (`sub_menu_id`,`menu_id`),
  KEY `menu_id_frk_idx` (`menu_id`),
  CONSTRAINT `menu_id_frk` FOREIGN KEY (`menu_id`) REFERENCES `menu_application` (`menu_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_menu_application`
--

LOCK TABLES `sub_menu_application` WRITE;
/*!40000 ALTER TABLE `sub_menu_application` DISABLE KEYS */;
INSERT INTO `sub_menu_application` VALUES (1,'All Schools','AllBusiness',1),(2,'All Users','allUser',2),(3,'Add Users Access','userSecurity',2),(4,'User History Access','userHistoryAccess',2),(5,'All Device','device',3),(6,'View Routes at Device','deviceViewRoute',3),(7,'View Users at Device','deviceViewUsers',3),(8,'All Routes','routes',4),(9,'All Stops','stops',5),(10,'View Users at Stops','stopViewUser',5),(11,'Manage Access Module','superUserOptions',6),(12,'Bulk Users','bulkUsers',6),(13,'Bulk Stops','bulkStops',6),(14,'Bulk School','bulkSchool',6);
/*!40000 ALTER TABLE `sub_menu_application` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `traffic`
--

DROP TABLE IF EXISTS `traffic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traffic` (
  `traffic_id` int(11) NOT NULL AUTO_INCREMENT,
  `traffic_type_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `access_id` int(11) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `traffic_info` varchar(2500) DEFAULT NULL,
  `device_id` int(11) NOT NULL,
  PRIMARY KEY (`traffic_id`),
  KEY `traffic_type_traffic_fk_idx` (`traffic_type_id`),
  CONSTRAINT `traffic_type_traffic_fk` FOREIGN KEY (`traffic_type_id`) REFERENCES `traffic_type` (`traffic_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `traffic`
--

LOCK TABLES `traffic` WRITE;
/*!40000 ALTER TABLE `traffic` DISABLE KEYS */;
/*!40000 ALTER TABLE `traffic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `traffic_type`
--

DROP TABLE IF EXISTS `traffic_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traffic_type` (
  `traffic_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `traffic_type_name` varchar(50) NOT NULL,
  PRIMARY KEY (`traffic_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `traffic_type`
--

LOCK TABLES `traffic_type` WRITE;
/*!40000 ALTER TABLE `traffic_type` DISABLE KEYS */;
INSERT INTO `traffic_type` VALUES (1,'IN'),(2,'OUT');
/*!40000 ALTER TABLE `traffic_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_access_data`
--

DROP TABLE IF EXISTS `user_access_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_access_data` (
  `user_access_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `access_type_id` int(11) NOT NULL,
  `user_login_name` varchar(60) DEFAULT NULL,
  `user_password` varchar(45) DEFAULT NULL,
  `document_id` varchar(45) DEFAULT NULL,
  `rfid_code` varchar(45) DEFAULT NULL,
  `biometric_code` varchar(2500) DEFAULT NULL,
  `status_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_access_data_id`,`user_id`,`access_type_id`),
  UNIQUE KEY `rfid_code_UNIQUE` (`rfid_code`),
  UNIQUE KEY `user_login_name_UNIQUE` (`user_login_name`),
  UNIQUE KEY `document_id_UNIQUE` (`document_id`),
  KEY `user_access_data_access_type_fk_idx` (`access_type_id`),
  KEY `user_access_data_user_fk_idx` (`user_id`),
  KEY `user_access_data_status_fk_idx` (`status_id`),
  CONSTRAINT `user_access_data_access_type_fk` FOREIGN KEY (`access_type_id`) REFERENCES `access_type` (`access_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `user_access_data_status_fk` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `user_access_data_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=149 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_access_data`
--

LOCK TABLES `user_access_data` WRITE;
/*!40000 ALTER TABLE `user_access_data` DISABLE KEYS */;
INSERT INTO `user_access_data` VALUES (1,1,1,'reaxiumUser','reaxiumPassword','0000007',NULL,NULL,1);
/*!40000 ALTER TABLE `user_access_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_type`
--

DROP TABLE IF EXISTS `user_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_type` (
  `user_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type_name` varchar(45) NOT NULL,
  PRIMARY KEY (`user_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_type`
--

LOCK TABLES `user_type` WRITE;
/*!40000 ALTER TABLE `user_type` DISABLE KEYS */;
INSERT INTO `user_type` VALUES (1,'administrator'),(2,'student'),(3,'stakeholder'),(4,'driver'),(5,'admin school'),(6,'call center');
/*!40000 ALTER TABLE `user_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` varchar(20) DEFAULT NULL,
  `first_name` varchar(70) NOT NULL,
  `second_name` varchar(70) DEFAULT NULL,
  `first_last_name` varchar(80) NOT NULL,
  `second_last_name` varchar(80) DEFAULT NULL,
  `status_id` int(11) NOT NULL DEFAULT '1',
  `user_type_id` int(11) DEFAULT NULL,
  `user_photo` varchar(1500) DEFAULT NULL,
  `business_id` int(11) NOT NULL DEFAULT '1',
  `email` varchar(80) DEFAULT NULL,
  `birthdate` varchar(15) DEFAULT NULL,
  `fingerprint` varchar(1500) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `document_id_UNIQUE` (`document_id`),
  KEY `users_status_fk_idx` (`status_id`),
  KEY `user_type_id_Rel_fk_idx` (`user_type_id`),
  KEY `user_business_rel_idx` (`business_id`),
  CONSTRAINT `users_status_fk` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `user_business_rel` FOREIGN KEY (`business_id`) REFERENCES `business` (`business_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `user_type_id_Rel_fk` FOREIGN KEY (`user_type_id`) REFERENCES `user_type` (`user_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=149 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'00000001','Admin','Reaxium','Reaxium',NULL,1,1,NULL,1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_access_control`
--

DROP TABLE IF EXISTS `users_access_control`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_access_control` (
  `access_id` int(11) NOT NULL AUTO_INCREMENT,
  `device_id` int(11) NOT NULL,
  `user_access_data_id` int(11) NOT NULL,
  PRIMARY KEY (`access_id`,`device_id`),
  KEY `deviceuser_access_fk_idx` (`device_id`),
  KEY `user_access_data_id_fk_idx` (`user_access_data_id`),
  CONSTRAINT `access_control_device_fk` FOREIGN KEY (`device_id`) REFERENCES `reaxium_device` (`device_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `user_access_data_id_fk` FOREIGN KEY (`user_access_data_id`) REFERENCES `user_access_data` (`user_access_data_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_access_control`
--

LOCK TABLES `users_access_control` WRITE;
/*!40000 ALTER TABLE `users_access_control` DISABLE KEYS */;
/*!40000 ALTER TABLE `users_access_control` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_relationship`
--

DROP TABLE IF EXISTS `users_relationship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_relationship` (
  `stakeholder_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`stakeholder_id`,`user_id`),
  KEY `user_rel_stakeholder_fk_idx` (`user_id`),
  CONSTRAINT `stakeholder_rel_users_fk` FOREIGN KEY (`stakeholder_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `user_rel_stakeholder_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_relationship`
--

LOCK TABLES `users_relationship` WRITE;
/*!40000 ALTER TABLE `users_relationship` DISABLE KEYS */;
/*!40000 ALTER TABLE `users_relationship` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-06-17 11:27:00
