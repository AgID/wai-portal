USE `matomo`;

--
-- Table structure for table `queuedtracking_queue`
--

DROP TABLE IF EXISTS `queuedtracking_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `queuedtracking_queue` (
  `queue_key` varchar(70) NOT NULL,
  `queue_value` varchar(255) DEFAULT NULL,
  `expiry_time` bigint(20) unsigned DEFAULT 9999999999,
  PRIMARY KEY (`queue_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
