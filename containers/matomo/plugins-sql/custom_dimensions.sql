USE `matomo`;

--
-- Table structure for table `custom_dimensions`
--

DROP TABLE IF EXISTS `custom_dimensions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custom_dimensions` (
  `idcustomdimension` bigint(20) unsigned NOT NULL,
  `idsite` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `index` smallint(5) unsigned NOT NULL,
  `scope` varchar(10) NOT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `extractions` text NOT NULL DEFAULT '',
  `case_sensitive` tinyint(3) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`idcustomdimension`,`idsite`),
  UNIQUE KEY `uniq_hash` (`idsite`,`scope`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

ALTER TABLE `log_conversion`
  ADD COLUMN `custom_dimension_1` varchar(255) DEFAULT NULL,
  ADD COLUMN `custom_dimension_2` varchar(255) DEFAULT NULL,
  ADD COLUMN `custom_dimension_3` varchar(255) DEFAULT NULL,
  ADD COLUMN `custom_dimension_4` varchar(255) DEFAULT NULL,
  ADD COLUMN `custom_dimension_5` varchar(255) DEFAULT NULL;

ALTER TABLE `log_visit`
  ADD COLUMN `custom_dimension_1` varchar(255) DEFAULT NULL,
  ADD COLUMN `custom_dimension_2` varchar(255) DEFAULT NULL,
  ADD COLUMN `custom_dimension_3` varchar(255) DEFAULT NULL,
  ADD COLUMN `custom_dimension_4` varchar(255) DEFAULT NULL,
  ADD COLUMN `custom_dimension_5` varchar(255) DEFAULT NULL,
  ADD COLUMN `last_idlink_va` bigint unsigned DEFAULT NULL;

ALTER TABLE `log_link_visit_action`
  ADD COLUMN `custom_dimension_1` varchar(255) DEFAULT NULL,
  ADD COLUMN `custom_dimension_2` varchar(255) DEFAULT NULL,
  ADD COLUMN `custom_dimension_3` varchar(255) DEFAULT NULL,
  ADD COLUMN `custom_dimension_4` varchar(255) DEFAULT NULL,
  ADD COLUMN `custom_dimension_5` varchar(255) DEFAULT NULL,
  ADD COLUMN `time_spent` int unsigned DEFAULT NULL;
