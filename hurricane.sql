-- MySQL dump 10.13  Distrib 5.7.19, for Linux (x86_64)
--
-- Host: localhost    Database: hurricane
-- ------------------------------------------------------
-- Server version	5.7.19-0ubuntu0.16.04.1

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
-- Table structure for table `APTypes`
--

DROP TABLE IF EXISTS `APTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APTypes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TypeName` varchar(32) DEFAULT NULL,
  `Provisionable` tinyint(1) DEFAULT NULL,
  `Vendor` varchar(20) DEFAULT NULL,
  `Model` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APTypes`
--

LOCK TABLES `APTypes` WRITE;
/*!40000 ALTER TABLE `APTypes` DISABLE KEYS */;
/*!40000 ALTER TABLE `APTypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `APs`
--

DROP TABLE IF EXISTS `APs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `APs` (
  `ID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `InventoryNumber` varchar(16) NOT NULL,
  `SerialNumber` varchar(16) NOT NULL,
  `APID` varchar(16) NOT NULL,
  `ColorCode` mediumint(9) NOT NULL,
  `MAC` varchar(12) NOT NULL,
  `IP` varchar(15) NOT NULL,
  `Status_ID` mediumint(9) NOT NULL,
  `POP` int(11) NOT NULL,
  `Azimuth` int(5) NOT NULL,
  `ElevationAGL` mediumint(9) NOT NULL,
  `RadiationAngle` int(6) NOT NULL,
  `Altitude` float NOT NULL,
  `ChBW` int(11) NOT NULL,
  `LocationName` varchar(64) NOT NULL,
  `LocationAddress` varchar(64) NOT NULL,
  `LocationCity` varchar(32) NOT NULL,
  `LocationState` varchar(2) NOT NULL,
  `LocationZIP` varchar(16) NOT NULL,
  `LocationGeocode` varchar(32) NOT NULL,
  `LocationPhone1` varchar(32) NOT NULL,
  `LocationPhone2` varchar(32) NOT NULL,
  `LocationPhone3` varchar(32) NOT NULL,
  `InstallDate` datetime NOT NULL,
  `Notes` varchar(1024) NOT NULL,
  `PatternKMLFileName` varchar(255) DEFAULT NULL,
  `Type` int(11) DEFAULT NULL,
  `Technology` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `APs`
--

LOCK TABLES `APs` WRITE;
/*!40000 ALTER TABLE `APs` DISABLE KEYS */;
INSERT INTO `APs` VALUES (0,'','','',0,'','',0,0,0,0,0,0,0,'','','','','','','','','','0000-00-00 00:00:00','',NULL,NULL,0,'2017-10-09 15:02:34');
/*!40000 ALTER TABLE `APs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Contracts`
--

DROP TABLE IF EXISTS `Contracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Contracts` (
  `ID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Contract` varchar(32) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Contracts`
--

LOCK TABLES `Contracts` WRITE;
/*!40000 ALTER TABLE `Contracts` DISABLE KEYS */;
INSERT INTO `Contracts` VALUES (1,'1-year');
/*!40000 ALTER TABLE `Contracts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `DataRates`
--

DROP TABLE IF EXISTS `DataRates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DataRates` (
  `ID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `DataRate` varchar(32) NOT NULL,
  `DownCIR` varchar(5) DEFAULT NULL,
  `DownMIR` varchar(5) DEFAULT NULL,
  `UpCIR` varchar(5) DEFAULT NULL,
  `UpMIR` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DataRates`
--

LOCK TABLES `DataRates` WRITE;
/*!40000 ALTER TABLE `DataRates` DISABLE KEYS */;
INSERT INTO `DataRates` VALUES (1,'10up5dn','10000','15000','5000','7500');
/*!40000 ALTER TABLE `DataRates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `IPs`
--

DROP TABLE IF EXISTS `IPs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IPs` (
  `network` int(11) unsigned NOT NULL,
  `mask` int(11) unsigned NOT NULL,
  `pool_start` varchar(11) NOT NULL DEFAULT '',
  `pool_end` varchar(11) NOT NULL DEFAULT '',
  PRIMARY KEY (`network`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `IPs`
--

LOCK TABLES `IPs` WRITE;
/*!40000 ALTER TABLE `IPs` DISABLE KEYS */;
/*!40000 ALTER TABLE `IPs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Installers`
--

DROP TABLE IF EXISTS `Installers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Installers` (
  `ID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Installer` varchar(32) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Installers`
--

LOCK TABLES `Installers` WRITE;
/*!40000 ALTER TABLE `Installers` DISABLE KEYS */;
/*!40000 ALTER TABLE `Installers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `LRAntennas`
--

DROP TABLE IF EXISTS `LRAntennas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LRAntennas` (
  `ID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `LRAntenna` varchar(32) NOT NULL,
  `ConfigGenerator` varchar(100) DEFAULT NULL,
  `FileFormat` varchar(4) DEFAULT NULL,
  `RadiusGenerator` varchar(100) DEFAULT NULL,
  `ConfigIP` varchar(15) NOT NULL DEFAULT '',
  `ConfigPort` tinyint(4) NOT NULL DEFAULT '0',
  `WizardScript` varchar(100) NOT NULL DEFAULT '',
  `Technology` int(11) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LRAntennas`
--

LOCK TABLES `LRAntennas` WRITE;
/*!40000 ALTER TABLE `LRAntennas` DISABLE KEYS */;
INSERT INTO `LRAntennas` VALUES (1,'LRA','CONFGEN','FILE','RADGEN','1.1.1.1',127,'WIZSCRIPT',0);
/*!40000 ALTER TABLE `LRAntennas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `POPs`
--

DROP TABLE IF EXISTS `POPs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `POPs` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `access` text NOT NULL,
  `location_owner` varchar(50) NOT NULL DEFAULT '',
  `management_company` varchar(50) NOT NULL DEFAULT '',
  `management_contact` text NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `elevation` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `POPs`
--

LOCK TABLES `POPs` WRITE;
/*!40000 ALTER TABLE `POPs` DISABLE KEYS */;
INSERT INTO `POPs` VALUES (0,'','','','','','','',0,0,0,'2017-10-09 15:13:57');
/*!40000 ALTER TABLE `POPs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `RiataQuery`
--

DROP TABLE IF EXISTS `RiataQuery`;
/*!50001 DROP VIEW IF EXISTS `RiataQuery`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `RiataQuery` AS SELECT 
 1 AS `APID`,
 1 AS `Status`,
 1 AS `LRAntenna`,
 1 AS `CustomerName`,
 1 AS `Account`,
 1 AS `DataRate`,
 1 AS `Notes`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `SUWizard`
--

DROP TABLE IF EXISTS `SUWizard`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SUWizard` (
  `port` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mac` varchar(20) NOT NULL DEFAULT '',
  `push` enum('0','1') NOT NULL DEFAULT '0',
  `running` enum('0','1') NOT NULL DEFAULT '0',
  `done` enum('0','1') NOT NULL DEFAULT '0',
  `status` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`port`),
  UNIQUE KEY `mac` (`mac`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SUWizard`
--

LOCK TABLES `SUWizard` WRITE;
/*!40000 ALTER TABLE `SUWizard` DISABLE KEYS */;
/*!40000 ALTER TABLE `SUWizard` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SUs`
--

DROP TABLE IF EXISTS `SUs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SUs` (
  `ID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `InventoryNumber` varchar(16) NOT NULL,
  `SerialNumber` varchar(16) NOT NULL,
  `AP_ID` mediumint(9) NOT NULL,
  `SUID` varchar(4) NOT NULL,
  `LRAntenna_ID` mediumint(9) NOT NULL,
  `MAC` varchar(12) NOT NULL,
  `IP` varchar(15) NOT NULL,
  `Status_ID` mediumint(9) NOT NULL,
  `CustomerName` varchar(64) NOT NULL,
  `CustomerAddress` varchar(64) NOT NULL,
  `CustomerCity` varchar(32) NOT NULL,
  `CustomerState` varchar(2) NOT NULL,
  `CustomerZIP` varchar(16) NOT NULL,
  `CustomerGeocode` varchar(32) NOT NULL,
  `CustomerPhone1` varchar(32) NOT NULL,
  `CustomerPhone2` varchar(32) NOT NULL,
  `CustomerPhone3` varchar(32) NOT NULL,
  `NetworkNumber` varchar(10) DEFAULT NULL,
  `InstallDate` datetime NOT NULL,
  `Installer_ID` mediumint(9) NOT NULL,
  `Contract_ID` mediumint(9) NOT NULL,
  `DataRate_ID` mediumint(9) NOT NULL,
  `Speed_Down` mediumint(9) NOT NULL,
  `Speed_Up` mediumint(9) NOT NULL,
  `Notes` varchar(1024) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SUs`
--

LOCK TABLES `SUs` WRITE;
/*!40000 ALTER TABLE `SUs` DISABLE KEYS */;
/*!40000 ALTER TABLE `SUs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SUs_backup`
--

DROP TABLE IF EXISTS `SUs_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SUs_backup` (
  `ID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `InventoryNumber` varchar(16) NOT NULL,
  `SerialNumber` varchar(16) NOT NULL,
  `AP_ID` mediumint(9) NOT NULL,
  `SUID` varchar(4) NOT NULL,
  `LRAntenna_ID` mediumint(9) NOT NULL,
  `MAC` varchar(12) NOT NULL,
  `IP` varchar(15) NOT NULL,
  `Status_ID` mediumint(9) NOT NULL,
  `CustomerName` varchar(64) NOT NULL,
  `CustomerAddress` varchar(64) NOT NULL,
  `CustomerCity` varchar(32) NOT NULL,
  `CustomerState` varchar(2) NOT NULL,
  `CustomerZIP` varchar(16) NOT NULL,
  `CustomerGeocode` varchar(32) NOT NULL,
  `CustomerPhone1` varchar(32) NOT NULL,
  `CustomerPhone2` varchar(32) NOT NULL,
  `CustomerPhone3` varchar(32) NOT NULL,
  `InstallDate` datetime NOT NULL,
  `Installer_ID` mediumint(9) NOT NULL,
  `Contract_ID` mediumint(9) NOT NULL,
  `DataRate_ID` mediumint(9) NOT NULL,
  `Speed_Down` mediumint(9) NOT NULL,
  `Speed_Up` mediumint(9) NOT NULL,
  `Notes` varchar(1024) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SUs_backup`
--

LOCK TABLES `SUs_backup` WRITE;
/*!40000 ALTER TABLE `SUs_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `SUs_backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Statuses`
--

DROP TABLE IF EXISTS `Statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Statuses` (
  `ID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Status` varchar(32) NOT NULL,
  `RowColor` varchar(24) NOT NULL,
  `RadiusAction` enum('No Action','Enable','Suspend','Delete') NOT NULL DEFAULT 'No Action',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Statuses`
--

LOCK TABLES `Statuses` WRITE;
/*!40000 ALTER TABLE `Statuses` DISABLE KEYS */;
INSERT INTO `Statuses` VALUES (1,'Inventory','78909c','No Action'),(2,'Pending','ffee58','No Action'),(3,'Installed','e0e0e0','No Action'),(4,'Abandoned','ef5350','No Action'),(5,'Testing','ff7043','No Action'),(6,'Bad','dd2c00','No Action');
/*!40000 ALTER TABLE `Statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Technologies`
--

DROP TABLE IF EXISTS `Technologies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Technologies` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `frequency` int(11) NOT NULL,
  `description` text NOT NULL,
  `maxSUs` int(11) NOT NULL,
  `maxBW` int(11) NOT NULL,
  `hasColorCode` tinyint(1) NOT NULL DEFAULT '0',
  `suHasExternalAntenna` tinyint(1) NOT NULL DEFAULT '0',
  `apHasExternalAntenna` tinyint(1) NOT NULL DEFAULT '0',
  `suManagementIP` int(10) unsigned NOT NULL,
  `apManagementIP` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Technologies`
--

LOCK TABLES `Technologies` WRITE;
/*!40000 ALTER TABLE `Technologies` DISABLE KEYS */;
/*!40000 ALTER TABLE `Technologies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `RiataQuery`
--

/*!50001 DROP VIEW IF EXISTS `RiataQuery`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `RiataQuery` AS select `APs`.`APID` AS `APID`,`Statuses`.`Status` AS `Status`,`LRAntennas`.`LRAntenna` AS `LRAntenna`,`SUs`.`CustomerName` AS `CustomerName`,`SUs`.`CustomerPhone3` AS `Account`,`DataRates`.`DataRate` AS `DataRate`,`SUs`.`Notes` AS `Notes` from ((((`SUs` left join `APs` on((`SUs`.`AP_ID` = `APs`.`ID`))) left join `Statuses` on((`SUs`.`Status_ID` = `Statuses`.`ID`))) left join `LRAntennas` on((`SUs`.`LRAntenna_ID` = `LRAntennas`.`ID`))) left join `DataRates` on((`SUs`.`DataRate_ID` = `DataRates`.`ID`))) where (`Statuses`.`Status` = 'Installed') */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-10-09 10:15:51
