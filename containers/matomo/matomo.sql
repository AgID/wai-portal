-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: mariadb
-- Creato il: Mar 29, 2018 alle 21:29
-- Versione del server: 10.2.13-MariaDB-10.2.13+maria~jessie
-- Versione PHP: 7.2.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `matomo`
--
DROP DATABASE IF EXISTS `matomo`;
CREATE DATABASE IF NOT EXISTS `matomo` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `matomo`;

-- --------------------------------------------------------

--
-- Struttura della tabella `access`
--

CREATE TABLE `access` (
  `login` varchar(100) NOT NULL,
  `idsite` int(10) UNSIGNED NOT NULL,
  `access` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `goal`
--

CREATE TABLE `goal` (
  `idsite` int(11) NOT NULL,
  `idgoal` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `match_attribute` varchar(20) NOT NULL,
  `pattern` varchar(255) NOT NULL,
  `pattern_type` varchar(10) NOT NULL,
  `case_sensitive` tinyint(4) NOT NULL,
  `allow_multiple` tinyint(4) NOT NULL,
  `revenue` float NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `logger_message`
--

CREATE TABLE `logger_message` (
  `idlogger_message` int(10) UNSIGNED NOT NULL,
  `tag` varchar(50) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `level` varchar(16) DEFAULT NULL,
  `message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `log_action`
--

CREATE TABLE `log_action` (
  `idaction` int(10) UNSIGNED NOT NULL,
  `name` text DEFAULT NULL,
  `hash` int(10) UNSIGNED NOT NULL,
  `type` tinyint(3) UNSIGNED DEFAULT NULL,
  `url_prefix` tinyint(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `log_conversion`
--

CREATE TABLE `log_conversion` (
  `idvisit` bigint(10) UNSIGNED NOT NULL,
  `idsite` int(10) UNSIGNED NOT NULL,
  `idvisitor` binary(8) NOT NULL,
  `server_time` datetime NOT NULL,
  `idaction_url` int(10) UNSIGNED DEFAULT NULL,
  `idlink_va` bigint(10) UNSIGNED DEFAULT NULL,
  `idgoal` int(10) NOT NULL,
  `buster` int(10) UNSIGNED NOT NULL,
  `idorder` varchar(100) DEFAULT NULL,
  `items` smallint(5) UNSIGNED DEFAULT NULL,
  `url` text NOT NULL,
  `visitor_days_since_first` smallint(5) UNSIGNED DEFAULT NULL,
  `visitor_days_since_order` smallint(5) UNSIGNED DEFAULT NULL,
  `visitor_returning` tinyint(1) DEFAULT NULL,
  `visitor_count_visits` int(11) UNSIGNED NOT NULL,
  `referer_keyword` varchar(255) DEFAULT NULL,
  `referer_name` varchar(70) DEFAULT NULL,
  `referer_type` tinyint(1) UNSIGNED DEFAULT NULL,
  `config_device_brand` varchar(100) DEFAULT NULL,
  `config_device_model` varchar(100) DEFAULT NULL,
  `config_device_type` tinyint(100) DEFAULT NULL,
  `location_city` varchar(255) DEFAULT NULL,
  `location_country` char(3) DEFAULT NULL,
  `location_latitude` decimal(9,6) DEFAULT NULL,
  `location_longitude` decimal(9,6) DEFAULT NULL,
  `location_region` char(2) DEFAULT NULL,
  `revenue` float DEFAULT NULL,
  `revenue_discount` float DEFAULT NULL,
  `revenue_shipping` float DEFAULT NULL,
  `revenue_subtotal` float DEFAULT NULL,
  `revenue_tax` float DEFAULT NULL,
  `custom_var_k1` varchar(200) DEFAULT NULL,
  `custom_var_v1` varchar(200) DEFAULT NULL,
  `custom_var_k2` varchar(200) DEFAULT NULL,
  `custom_var_v2` varchar(200) DEFAULT NULL,
  `custom_var_k3` varchar(200) DEFAULT NULL,
  `custom_var_v3` varchar(200) DEFAULT NULL,
  `custom_var_k4` varchar(200) DEFAULT NULL,
  `custom_var_v4` varchar(200) DEFAULT NULL,
  `custom_var_k5` varchar(200) DEFAULT NULL,
  `custom_var_v5` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `log_conversion_item`
--

CREATE TABLE `log_conversion_item` (
  `idsite` int(10) UNSIGNED NOT NULL,
  `idvisitor` binary(8) NOT NULL,
  `server_time` datetime NOT NULL,
  `idvisit` bigint(10) UNSIGNED NOT NULL,
  `idorder` varchar(100) NOT NULL,
  `idaction_sku` int(10) UNSIGNED NOT NULL,
  `idaction_name` int(10) UNSIGNED NOT NULL,
  `idaction_category` int(10) UNSIGNED NOT NULL,
  `idaction_category2` int(10) UNSIGNED NOT NULL,
  `idaction_category3` int(10) UNSIGNED NOT NULL,
  `idaction_category4` int(10) UNSIGNED NOT NULL,
  `idaction_category5` int(10) UNSIGNED NOT NULL,
  `price` float NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `deleted` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `log_link_visit_action`
--

CREATE TABLE `log_link_visit_action` (
  `idlink_va` bigint(10) UNSIGNED NOT NULL,
  `idsite` int(10) UNSIGNED NOT NULL,
  `idvisitor` binary(8) NOT NULL,
  `idvisit` bigint(10) UNSIGNED NOT NULL,
  `idaction_url_ref` int(10) UNSIGNED DEFAULT 0,
  `idaction_name_ref` int(10) UNSIGNED DEFAULT NULL,
  `custom_float` float DEFAULT NULL,
  `server_time` datetime NOT NULL,
  `idpageview` char(6) DEFAULT NULL,
  `interaction_position` smallint(5) UNSIGNED DEFAULT NULL,
  `idaction_name` int(10) UNSIGNED DEFAULT NULL,
  `idaction_url` int(10) UNSIGNED DEFAULT NULL,
  `time_spent_ref_action` int(10) UNSIGNED DEFAULT NULL,
  `idaction_event_action` int(10) UNSIGNED DEFAULT NULL,
  `idaction_event_category` int(10) UNSIGNED DEFAULT NULL,
  `idaction_content_interaction` int(10) UNSIGNED DEFAULT NULL,
  `idaction_content_name` int(10) UNSIGNED DEFAULT NULL,
  `idaction_content_piece` int(10) UNSIGNED DEFAULT NULL,
  `idaction_content_target` int(10) UNSIGNED DEFAULT NULL,
  `custom_var_k1` varchar(200) DEFAULT NULL,
  `custom_var_v1` varchar(200) DEFAULT NULL,
  `custom_var_k2` varchar(200) DEFAULT NULL,
  `custom_var_v2` varchar(200) DEFAULT NULL,
  `custom_var_k3` varchar(200) DEFAULT NULL,
  `custom_var_v3` varchar(200) DEFAULT NULL,
  `custom_var_k4` varchar(200) DEFAULT NULL,
  `custom_var_v4` varchar(200) DEFAULT NULL,
  `custom_var_k5` varchar(200) DEFAULT NULL,
  `custom_var_v5` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `log_profiling`
--

CREATE TABLE `log_profiling` (
  `query` text NOT NULL,
  `count` int(10) UNSIGNED DEFAULT NULL,
  `sum_time_ms` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `log_visit`
--

CREATE TABLE `log_visit` (
  `idvisit` bigint(10) UNSIGNED NOT NULL,
  `idsite` int(10) UNSIGNED NOT NULL,
  `idvisitor` binary(8) NOT NULL,
  `visit_last_action_time` datetime NOT NULL,
  `config_id` binary(8) NOT NULL,
  `location_ip` varbinary(16) NOT NULL,
  `user_id` varchar(200) DEFAULT NULL,
  `visit_first_action_time` datetime NOT NULL,
  `visit_goal_buyer` tinyint(1) DEFAULT NULL,
  `visit_goal_converted` tinyint(1) DEFAULT NULL,
  `visitor_days_since_first` smallint(5) UNSIGNED DEFAULT NULL,
  `visitor_days_since_order` smallint(5) UNSIGNED DEFAULT NULL,
  `visitor_returning` tinyint(1) DEFAULT NULL,
  `visitor_count_visits` int(11) UNSIGNED NOT NULL,
  `visit_entry_idaction_name` int(10) UNSIGNED DEFAULT NULL,
  `visit_entry_idaction_url` int(11) UNSIGNED DEFAULT NULL,
  `visit_exit_idaction_name` int(10) UNSIGNED DEFAULT NULL,
  `visit_exit_idaction_url` int(10) UNSIGNED DEFAULT 0,
  `visit_total_actions` int(11) UNSIGNED DEFAULT NULL,
  `visit_total_interactions` smallint(5) UNSIGNED DEFAULT 0,
  `visit_total_searches` smallint(5) UNSIGNED DEFAULT NULL,
  `referer_keyword` varchar(255) DEFAULT NULL,
  `referer_name` varchar(70) DEFAULT NULL,
  `referer_type` tinyint(1) UNSIGNED DEFAULT NULL,
  `referer_url` text DEFAULT NULL,
  `location_browser_lang` varchar(20) DEFAULT NULL,
  `config_browser_engine` varchar(10) DEFAULT NULL,
  `config_browser_name` varchar(10) DEFAULT NULL,
  `config_browser_version` varchar(20) DEFAULT NULL,
  `config_device_brand` varchar(100) DEFAULT NULL,
  `config_device_model` varchar(100) DEFAULT NULL,
  `config_device_type` tinyint(100) DEFAULT NULL,
  `config_os` char(3) DEFAULT NULL,
  `config_os_version` varchar(100) DEFAULT NULL,
  `visit_total_events` int(11) UNSIGNED DEFAULT NULL,
  `visitor_localtime` time DEFAULT NULL,
  `visitor_days_since_last` smallint(5) UNSIGNED DEFAULT NULL,
  `config_resolution` varchar(18) DEFAULT NULL,
  `config_cookie` tinyint(1) DEFAULT NULL,
  `config_director` tinyint(1) DEFAULT NULL,
  `config_flash` tinyint(1) DEFAULT NULL,
  `config_gears` tinyint(1) DEFAULT NULL,
  `config_java` tinyint(1) DEFAULT NULL,
  `config_pdf` tinyint(1) DEFAULT NULL,
  `config_quicktime` tinyint(1) DEFAULT NULL,
  `config_realplayer` tinyint(1) DEFAULT NULL,
  `config_silverlight` tinyint(1) DEFAULT NULL,
  `config_windowsmedia` tinyint(1) DEFAULT NULL,
  `visit_total_time` int(11) UNSIGNED NOT NULL,
  `location_city` varchar(255) DEFAULT NULL,
  `location_country` char(3) DEFAULT NULL,
  `location_latitude` decimal(9,6) DEFAULT NULL,
  `location_longitude` decimal(9,6) DEFAULT NULL,
  `location_region` char(2) DEFAULT NULL,
  `custom_var_k1` varchar(200) DEFAULT NULL,
  `custom_var_v1` varchar(200) DEFAULT NULL,
  `custom_var_k2` varchar(200) DEFAULT NULL,
  `custom_var_v2` varchar(200) DEFAULT NULL,
  `custom_var_k3` varchar(200) DEFAULT NULL,
  `custom_var_v3` varchar(200) DEFAULT NULL,
  `custom_var_k4` varchar(200) DEFAULT NULL,
  `custom_var_v4` varchar(200) DEFAULT NULL,
  `custom_var_k5` varchar(200) DEFAULT NULL,
  `custom_var_v5` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `option`
--

CREATE TABLE `option` (
  `option_name` varchar(255) NOT NULL,
  `option_value` longtext NOT NULL,
  `autoload` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `option`
--

INSERT INTO `option` (`option_name`, `option_value`, `autoload`) VALUES
  ('lastTrackerCronRun', '1520431070', 0),
  ('MobileMessaging_DelegatedManagement', 'false', 0),
  ('piwikUrl', 'https://localhost:9443/', 1),
  ('PrivacyManager.doNotTrackEnabled', '1', 0),
  ('PrivacyManager.ipAnonymizerEnabled', '1', 0),
  ('SitesManager_DefaultCurrency', 'EUR', 0),
  ('SitesManager_DefaultTimezone', 'Europe/Rome', 0),
  ('UpdateCheck_LastTimeChecked', '1522358874', 1),
  ('UpdateCheck_LatestVersion', '3.4.0', 0),
  ('usercountry.location_provider', 'geoip_php', 0),
  ('UsersManager.lastSeen.root', '1522358874', 1),
  ('version_Actions', '3.4.0', 1),
  ('version_Annotations', '3.4.0', 1),
  ('version_API', '3.4.0', 1),
  ('version_BulkTracking', '3.4.0', 1),
  ('version_Contents', '3.4.0', 1),
  ('version_core', '3.4.0', 1),
  ('version_CoreAdminHome', '3.4.0', 1),
  ('version_CoreConsole', '3.4.0', 1),
  ('version_CoreHome', '3.4.0', 1),
  ('version_CorePluginsAdmin', '3.4.0', 1),
  ('version_CoreUpdater', '3.4.0', 1),
  ('version_CoreVisualizations', '3.4.0', 1),
  ('version_CustomPiwikJs', '3.4.0', 1),
  ('version_CustomVariables', '3.4.0', 1),
  ('version_Dashboard', '3.4.0', 1),
  ('version_DevicePlugins', '3.4.0', 1),
  ('version_DevicesDetection', '3.4.0', 1),
  ('version_Diagnostics', '3.4.0', 1),
  ('version_Ecommerce', '3.4.0', 1),
  ('version_Events', '3.4.0', 1),
  ('version_ExampleAPI', '1.0', 1),
  ('version_ExamplePlugin', '0.1.0', 1),
  ('version_Feedback', '3.4.0', 1),
  ('version_Goals', '3.4.0', 1),
  ('version_Heartbeat', '3.4.0', 1),
  ('version_ImageGraph', '3.4.0', 1),
  ('version_Insights', '3.4.0', 1),
  ('version_Installation', '3.4.0', 1),
  ('version_Intl', '3.4.0', 1),
  ('version_LanguagesManager', '3.4.0', 1),
  ('version_Live', '3.4.0', 1),
  ('version_Login', '3.4.0', 1),
  ('version_log_conversion.revenue', 'float default NULL', 1),
  ('version_log_conversion.revenue_discount', 'float default NULL', 1),
  ('version_log_conversion.revenue_shipping', 'float default NULL', 1),
  ('version_log_conversion.revenue_subtotal', 'float default NULL', 1),
  ('version_log_conversion.revenue_tax', 'float default NULL', 1),
  ('version_log_link_visit_action.idaction_content_interaction', 'INTEGER(10) UNSIGNED DEFAULT NULL', 1),
  ('version_log_link_visit_action.idaction_content_name', 'INTEGER(10) UNSIGNED DEFAULT NULL', 1),
  ('version_log_link_visit_action.idaction_content_piece', 'INTEGER(10) UNSIGNED DEFAULT NULL', 1),
  ('version_log_link_visit_action.idaction_content_target', 'INTEGER(10) UNSIGNED DEFAULT NULL', 1),
  ('version_log_link_visit_action.idaction_event_action', 'INTEGER(10) UNSIGNED DEFAULT NULL', 1),
  ('version_log_link_visit_action.idaction_event_category', 'INTEGER(10) UNSIGNED DEFAULT NULL', 1),
  ('version_log_link_visit_action.idaction_name', 'INTEGER(10) UNSIGNED', 1),
  ('version_log_link_visit_action.idaction_url', 'INTEGER(10) UNSIGNED DEFAULT NULL', 1),
  ('version_log_link_visit_action.idpageview', 'CHAR(6) NULL DEFAULT NULL', 1),
  ('version_log_link_visit_action.interaction_position', 'SMALLINT UNSIGNED DEFAULT NULL', 1),
  ('version_log_link_visit_action.server_time', 'DATETIME NOT NULL', 1),
  ('version_log_link_visit_action.time_spent_ref_action', 'INTEGER(10) UNSIGNED NULL', 1),
  ('version_log_visit.config_browser_engine', 'VARCHAR(10) NULL', 1),
  ('version_log_visit.config_browser_name', 'VARCHAR(10) NULL', 1),
  ('version_log_visit.config_browser_version', 'VARCHAR(20) NULL', 1),
  ('version_log_visit.config_cookie', 'TINYINT(1) NULL', 1),
  ('version_log_visit.config_device_brand', 'VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL1', 1),
  ('version_log_visit.config_device_model', 'VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL1', 1),
  ('version_log_visit.config_device_type', 'TINYINT( 100 ) NULL DEFAULT NULL1', 1),
  ('version_log_visit.config_director', 'TINYINT(1) NULL', 1),
  ('version_log_visit.config_flash', 'TINYINT(1) NULL', 1),
  ('version_log_visit.config_gears', 'TINYINT(1) NULL', 1),
  ('version_log_visit.config_java', 'TINYINT(1) NULL', 1),
  ('version_log_visit.config_os', 'CHAR(3) NULL', 1),
  ('version_log_visit.config_os_version', 'VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL', 1),
  ('version_log_visit.config_pdf', 'TINYINT(1) NULL', 1),
  ('version_log_visit.config_quicktime', 'TINYINT(1) NULL', 1),
  ('version_log_visit.config_realplayer', 'TINYINT(1) NULL', 1),
  ('version_log_visit.config_resolution', 'VARCHAR(18) NULL', 1),
  ('version_log_visit.config_silverlight', 'TINYINT(1) NULL', 1),
  ('version_log_visit.config_windowsmedia', 'TINYINT(1) NULL', 1),
  ('version_log_visit.location_browser_lang', 'VARCHAR(20) NULL', 1),
  ('version_log_visit.location_city', 'varchar(255) DEFAULT NULL1', 1),
  ('version_log_visit.location_country', 'CHAR(3) NULL1', 1),
  ('version_log_visit.location_latitude', 'decimal(9, 6) DEFAULT NULL1', 1),
  ('version_log_visit.location_longitude', 'decimal(9, 6) DEFAULT NULL1', 1),
  ('version_log_visit.location_region', 'char(2) DEFAULT NULL1', 1),
  ('version_log_visit.referer_keyword', 'VARCHAR(255) NULL1', 1),
  ('version_log_visit.referer_name', 'VARCHAR(70) NULL1', 1),
  ('version_log_visit.referer_type', 'TINYINT(1) UNSIGNED NULL1', 1),
  ('version_log_visit.referer_url', 'TEXT NULL', 1),
  ('version_log_visit.user_id', 'VARCHAR(200) NULL', 1),
  ('version_log_visit.visitor_count_visits', 'INT(11) UNSIGNED NOT NULL1', 1),
  ('version_log_visit.visitor_days_since_first', 'SMALLINT(5) UNSIGNED NULL1', 1),
  ('version_log_visit.visitor_days_since_last', 'SMALLINT(5) UNSIGNED NULL', 1),
  ('version_log_visit.visitor_days_since_order', 'SMALLINT(5) UNSIGNED NULL1', 1),
  ('version_log_visit.visitor_localtime', 'TIME NULL', 1),
  ('version_log_visit.visitor_returning', 'TINYINT(1) NULL1', 1),
  ('version_log_visit.visit_entry_idaction_name', 'INTEGER(10) UNSIGNED NULL', 1),
  ('version_log_visit.visit_entry_idaction_url', 'INTEGER(11) UNSIGNED NULL  DEFAULT NULL', 1),
  ('version_log_visit.visit_exit_idaction_name', 'INTEGER(10) UNSIGNED NULL', 1),
  ('version_log_visit.visit_exit_idaction_url', 'INTEGER(10) UNSIGNED NULL DEFAULT 0', 1),
  ('version_log_visit.visit_first_action_time', 'DATETIME NOT NULL', 1),
  ('version_log_visit.visit_goal_buyer', 'TINYINT(1) NULL', 1),
  ('version_log_visit.visit_goal_converted', 'TINYINT(1) NULL', 1),
  ('version_log_visit.visit_total_actions', 'INT(11) UNSIGNED NULL', 1),
  ('version_log_visit.visit_total_events', 'INT(11) UNSIGNED NULL', 1),
  ('version_log_visit.visit_total_interactions', 'SMALLINT UNSIGNED DEFAULT 0', 1),
  ('version_log_visit.visit_total_searches', 'SMALLINT(5) UNSIGNED NULL', 1),
  ('version_log_visit.visit_total_time', 'INT(11) UNSIGNED NOT NULL', 1),
  ('version_Marketplace', '3.4.0', 1),
  ('version_MobileMessaging', '3.4.0', 1),
  ('version_Monolog', '3.4.0', 1),
  ('version_Morpheus', '3.4.0', 1),
  ('version_MultiSites', '3.4.0', 1),
  ('version_Overlay', '3.4.0', 1),
  ('version_PrivacyManager', '3.4.0', 1),
  ('version_ProfessionalServices', '3.4.0', 1),
  ('version_Proxy', '3.4.0', 1),
  ('version_Referrers', '3.4.0', 1),
  ('version_Resolution', '3.4.0', 1),
  ('version_RssWidget', '1.0', 1),
  ('version_ScheduledReports', '3.4.0', 1),
  ('version_SegmentEditor', '3.4.0', 1),
  ('version_SEO', '3.4.0', 1),
  ('version_SitesManager', '3.4.0', 1),
  ('version_Transitions', '3.4.0', 1),
  ('version_UserCountry', '3.4.0', 1),
  ('version_UserCountryMap', '3.4.0', 1),
  ('version_UserId', '3.4.0', 1),
  ('version_UserLanguage', '3.4.0', 1),
  ('version_UsersManager', '3.4.0', 1),
  ('version_VisitFrequency', '3.4.0', 1),
  ('version_VisitorInterest', '3.4.0', 1),
  ('version_VisitsSummary', '3.4.0', 1),
  ('version_VisitTime', '3.4.0', 1),
  ('version_WebsiteMeasurable', '3.4.0', 1),
  ('version_Widgetize', '3.4.0', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `plugin_setting`
--

CREATE TABLE `plugin_setting` (
  `plugin_name` varchar(60) NOT NULL,
  `setting_name` varchar(255) NOT NULL,
  `setting_value` longtext NOT NULL,
  `user_login` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `report`
--

CREATE TABLE `report` (
  `idreport` int(11) NOT NULL,
  `idsite` int(11) NOT NULL,
  `login` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `idsegment` int(11) DEFAULT NULL,
  `period` varchar(10) NOT NULL,
  `hour` tinyint(4) NOT NULL DEFAULT 0,
  `type` varchar(10) NOT NULL,
  `format` varchar(10) NOT NULL,
  `reports` text NOT NULL,
  `parameters` text DEFAULT NULL,
  `ts_created` timestamp NULL DEFAULT NULL,
  `ts_last_sent` timestamp NULL DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `segment`
--

CREATE TABLE `segment` (
  `idsegment` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `definition` text NOT NULL,
  `login` varchar(100) NOT NULL,
  `enable_all_users` tinyint(4) NOT NULL DEFAULT 0,
  `enable_only_idsite` int(11) DEFAULT NULL,
  `auto_archive` tinyint(4) NOT NULL DEFAULT 0,
  `ts_created` timestamp NULL DEFAULT NULL,
  `ts_last_edit` timestamp NULL DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `sequence`
--

CREATE TABLE `sequence` (
  `name` varchar(120) NOT NULL,
  `value` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `session`
--

CREATE TABLE `session` (
  `id` varchar(255) NOT NULL,
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `site`
--

CREATE TABLE `site` (
  `idsite` int(10) UNSIGNED NOT NULL,
  `name` varchar(90) NOT NULL,
  `main_url` varchar(255) NOT NULL,
  `ts_created` timestamp NULL DEFAULT NULL,
  `ecommerce` tinyint(4) DEFAULT 0,
  `sitesearch` tinyint(4) DEFAULT 1,
  `sitesearch_keyword_parameters` text NOT NULL,
  `sitesearch_category_parameters` text NOT NULL,
  `timezone` varchar(50) NOT NULL,
  `currency` char(3) NOT NULL,
  `exclude_unknown_urls` tinyint(1) DEFAULT 0,
  `excluded_ips` text NOT NULL,
  `excluded_parameters` text NOT NULL,
  `excluded_user_agents` text NOT NULL,
  `group` varchar(250) NOT NULL,
  `type` varchar(255) NOT NULL,
  `keep_url_fragment` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `site`
--

INSERT INTO `site` (`idsite`, `name`, `main_url`, `ts_created`, `ecommerce`, `sitesearch`, `sitesearch_keyword_parameters`, `sitesearch_category_parameters`, `timezone`, `currency`, `exclude_unknown_urls`, `excluded_ips`, `excluded_parameters`, `excluded_user_agents`, `group`, `type`, `keep_url_fragment`) VALUES
  (1, 'Web Analytics Italia', 'http://localhost', '2018-03-12 00:20:00', 0, 1, '', '', 'Europe/Rome', 'EUR', 0, '', '', '', '', 'website', 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `site_setting`
--

CREATE TABLE `site_setting` (
  `idsite` int(10) UNSIGNED NOT NULL,
  `plugin_name` varchar(60) NOT NULL,
  `setting_name` varchar(255) NOT NULL,
  `setting_value` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `site_url`
--

CREATE TABLE `site_url` (
  `idsite` int(10) UNSIGNED NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `user`
--

CREATE TABLE `user` (
  `login` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `alias` varchar(45) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token_auth` char(32) NOT NULL,
  `superuser_access` tinyint(2) UNSIGNED NOT NULL DEFAULT 0,
  `date_registered` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `user`
--

INSERT INTO `user` (`login`, `password`, `alias`, `email`, `token_auth`, `superuser_access`, `date_registered`) VALUES
  ('anonymous', '', 'anonymous', 'anonymous@example.org', 'anonymous', 0, '2018-02-16 22:40:14'),
  ('root', '$2y$10$fi2e6ABIBujBbqzXRB.BQeE9Ttfh08P.yfUJbRMJkJ40WCNltxoMO', 'root', 'root@example.com', '23e57891f724dd4be125c4adcec8fac0', 1, '2018-02-16 22:41:00');

-- --------------------------------------------------------

--
-- Struttura della tabella `user_dashboard`
--

CREATE TABLE `user_dashboard` (
  `login` varchar(100) NOT NULL,
  `iddashboard` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `layout` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `user_language`
--

CREATE TABLE `user_language` (
  `login` varchar(100) NOT NULL,
  `language` varchar(10) NOT NULL,
  `use_12_hour_clock` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `access`
--
ALTER TABLE `access`
  ADD PRIMARY KEY (`login`,`idsite`);

--
-- Indici per le tabelle `goal`
--
ALTER TABLE `goal`
  ADD PRIMARY KEY (`idsite`,`idgoal`);

--
-- Indici per le tabelle `logger_message`
--
ALTER TABLE `logger_message`
  ADD PRIMARY KEY (`idlogger_message`);

--
-- Indici per le tabelle `log_action`
--
ALTER TABLE `log_action`
  ADD PRIMARY KEY (`idaction`),
  ADD KEY `index_type_hash` (`type`,`hash`);

--
-- Indici per le tabelle `log_conversion`
--
ALTER TABLE `log_conversion`
  ADD PRIMARY KEY (`idvisit`,`idgoal`,`buster`),
  ADD UNIQUE KEY `unique_idsite_idorder` (`idsite`,`idorder`),
  ADD KEY `index_idsite_datetime` (`idsite`,`server_time`);

--
-- Indici per le tabelle `log_conversion_item`
--
ALTER TABLE `log_conversion_item`
  ADD PRIMARY KEY (`idvisit`,`idorder`,`idaction_sku`),
  ADD KEY `index_idsite_servertime` (`idsite`,`server_time`);

--
-- Indici per le tabelle `log_link_visit_action`
--
ALTER TABLE `log_link_visit_action`
  ADD PRIMARY KEY (`idlink_va`),
  ADD KEY `index_idvisit` (`idvisit`),
  ADD KEY `index_idsite_servertime` (`idsite`,`server_time`);

--
-- Indici per le tabelle `log_profiling`
--
ALTER TABLE `log_profiling`
  ADD UNIQUE KEY `query` (`query`(100));

--
-- Indici per le tabelle `log_visit`
--
ALTER TABLE `log_visit`
  ADD PRIMARY KEY (`idvisit`),
  ADD KEY `index_idsite_config_datetime` (`idsite`,`config_id`,`visit_last_action_time`),
  ADD KEY `index_idsite_datetime` (`idsite`,`visit_last_action_time`),
  ADD KEY `index_idsite_idvisitor` (`idsite`,`idvisitor`);

--
-- Indici per le tabelle `option`
--
ALTER TABLE `option`
  ADD PRIMARY KEY (`option_name`),
  ADD KEY `autoload` (`autoload`);

--
-- Indici per le tabelle `plugin_setting`
--
ALTER TABLE `plugin_setting`
  ADD KEY `plugin_name` (`plugin_name`,`user_login`);

--
-- Indici per le tabelle `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`idreport`);

--
-- Indici per le tabelle `segment`
--
ALTER TABLE `segment`
  ADD PRIMARY KEY (`idsegment`);

--
-- Indici per le tabelle `sequence`
--
ALTER TABLE `sequence`
  ADD PRIMARY KEY (`name`);

--
-- Indici per le tabelle `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `site`
--
ALTER TABLE `site`
  ADD PRIMARY KEY (`idsite`);

--
-- Indici per le tabelle `site_setting`
--
ALTER TABLE `site_setting`
  ADD KEY `idsite` (`idsite`,`plugin_name`);

--
-- Indici per le tabelle `site_url`
--
ALTER TABLE `site_url`
  ADD PRIMARY KEY (`idsite`,`url`);

--
-- Indici per le tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`login`),
  ADD UNIQUE KEY `uniq_keytoken` (`token_auth`);

--
-- Indici per le tabelle `user_dashboard`
--
ALTER TABLE `user_dashboard`
  ADD PRIMARY KEY (`login`,`iddashboard`);

--
-- Indici per le tabelle `user_language`
--
ALTER TABLE `user_language`
  ADD PRIMARY KEY (`login`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `logger_message`
--
ALTER TABLE `logger_message`
  MODIFY `idlogger_message` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `log_action`
--
ALTER TABLE `log_action`
  MODIFY `idaction` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `log_link_visit_action`
--
ALTER TABLE `log_link_visit_action`
  MODIFY `idlink_va` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `log_visit`
--
ALTER TABLE `log_visit`
  MODIFY `idvisit` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `report`
--
ALTER TABLE `report`
  MODIFY `idreport` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `segment`
--
ALTER TABLE `segment`
  MODIFY `idsegment` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `site`
--
ALTER TABLE `site`
  MODIFY `idsite` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;
