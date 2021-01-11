; <?php exit; ?> DO NOT REMOVE THIS LINE
; file automatically generated or modified by Matomo; you can manually override the default values in global.ini.php by redefining them in this file.
[database]
host = "mariadb"
username = "@MATOMO_DB_USER@"
password = "@MATOMO_DB_PASSWORD@"
dbname = "matomo"

[database_reader]
host = "@MATOMO_DB_READER@"
username = "@MATOMO_DB_USER@"
password = "@MATOMO_DB_PASSWORD@"
dbname = "matomo"
port = 3306

[General]
salt = "01106e8d05b7c61b81cbf572dccc977c"
trusted_hosts[] = "@MATOMO_WAI_URL@:9443"
enable_trusted_host_check = 1
force_ssl = 1
enable_update_users_email = 0
emails_enabled = 0
assume_secure_protocol = 1
enable_update_communication = 0
enable_auto_update = 0
show_update_notification_to_superusers_only = 1
enable_delete_old_data_settings_admin = 0
enable_geolocation_admin = 0
enable_installer = 0
enable_plugins_admin = 0
proxy_uri_header = 1
enable_general_settings_admin = 0
browser_archiving_disabled_enforce = @MATOMO_CRON_ARCHIVING_ENABLED@
enable_browser_archiving_triggering = @MATOMO_CRON_ARCHIVING_DISABLED@
default_day = "previous30"
default_period = "range"
default_language = "it"
archiving_custom_ranges[] = previous30
enable_processing_unique_visitors_range = 1

[Debug]
;always_archive_data_day=1
;always_archive_data_period=1
enable_create_realtime_segments = 0
allow_adding_segments_for_all_websites = 0
process_new_segments_from = "last30"

[Plugins]
Plugins[] = "CorePluginsAdmin"
Plugins[] = "CoreAdminHome"
Plugins[] = "CoreHome"
Plugins[] = "WebsiteMeasurable"
Plugins[] = "IntranetMeasurable"
Plugins[] = "Diagnostics"
Plugins[] = "CoreVisualizations"
Plugins[] = "Proxy"
Plugins[] = "API"
Plugins[] = "Widgetize"
Plugins[] = "Transitions"
Plugins[] = "LanguagesManager"
Plugins[] = "Actions"
Plugins[] = "Dashboard"
Plugins[] = "MultiSites"
Plugins[] = "Referrers"
Plugins[] = "UserLanguage"
Plugins[] = "DevicesDetection"
Plugins[] = "Goals"
Plugins[] = "Ecommerce"
Plugins[] = "SEO"
Plugins[] = "Events"
Plugins[] = "UserCountry"
Plugins[] = "GeoIp2"
Plugins[] = "VisitsSummary"
Plugins[] = "VisitFrequency"
Plugins[] = "VisitTime"
Plugins[] = "VisitorInterest"
Plugins[] = "Monolog"
Plugins[] = "Login"
Plugins[] = "UsersManager"
Plugins[] = "SitesManager"
Plugins[] = "Installation"
Plugins[] = "CoreUpdater"
Plugins[] = "CoreConsole"
Plugins[] = "ScheduledReports"
Plugins[] = "UserCountryMap"
Plugins[] = "Live"
Plugins[] = "CustomVariables"
Plugins[] = "PrivacyManager"
Plugins[] = "ImageGraph"
Plugins[] = "Annotations"
Plugins[] = "SegmentEditor"
Plugins[] = "Insights"
Plugins[] = "Morpheus"
Plugins[] = "Contents"
Plugins[] = "BulkTracking"
Plugins[] = "Resolution"
Plugins[] = "DevicePlugins"
Plugins[] = "Heartbeat"
Plugins[] = "Intl"
Plugins[] = "UserId"
Plugins[] = "CustomPiwikJs"
Plugins[] = "WAIMatomoTheme"
@MATOMO_PLUGINS@

[PluginsInstalled]
PluginsInstalled[] = "Diagnostics"
PluginsInstalled[] = "Login"
PluginsInstalled[] = "CoreAdminHome"
PluginsInstalled[] = "UsersManager"
PluginsInstalled[] = "SitesManager"
PluginsInstalled[] = "Installation"
PluginsInstalled[] = "Monolog"
PluginsInstalled[] = "Intl"
PluginsInstalled[] = "CorePluginsAdmin"
PluginsInstalled[] = "CoreHome"
PluginsInstalled[] = "WebsiteMeasurable"
PluginsInstalled[] = "IntranetMeasurable"
PluginsInstalled[] = "CoreVisualizations"
PluginsInstalled[] = "Proxy"
PluginsInstalled[] = "API"
PluginsInstalled[] = "Widgetize"
PluginsInstalled[] = "Transitions"
PluginsInstalled[] = "LanguagesManager"
PluginsInstalled[] = "Actions"
PluginsInstalled[] = "Dashboard"
PluginsInstalled[] = "MultiSites"
PluginsInstalled[] = "Referrers"
PluginsInstalled[] = "UserLanguage"
PluginsInstalled[] = "DevicesDetection"
PluginsInstalled[] = "Goals"
PluginsInstalled[] = "Ecommerce"
PluginsInstalled[] = "SEO"
PluginsInstalled[] = "Events"
PluginsInstalled[] = "UserCountry"
PluginsInstalled[] = "GeoIp2"
PluginsInstalled[] = "VisitsSummary"
PluginsInstalled[] = "VisitFrequency"
PluginsInstalled[] = "VisitTime"
PluginsInstalled[] = "VisitorInterest"
PluginsInstalled[] = "RssWidget"
PluginsInstalled[] = "TwoFactorAuth"
PluginsInstalled[] = "CoreUpdater"
PluginsInstalled[] = "CoreConsole"
PluginsInstalled[] = "ScheduledReports"
PluginsInstalled[] = "UserCountryMap"
PluginsInstalled[] = "Live"
PluginsInstalled[] = "CustomVariables"
PluginsInstalled[] = "PrivacyManager"
PluginsInstalled[] = "ImageGraph"
PluginsInstalled[] = "Annotations"
PluginsInstalled[] = "SegmentEditor"
PluginsInstalled[] = "Insights"
PluginsInstalled[] = "Morpheus"
PluginsInstalled[] = "Contents"
PluginsInstalled[] = "BulkTracking"
PluginsInstalled[] = "Resolution"
PluginsInstalled[] = "DevicePlugins"
PluginsInstalled[] = "Heartbeat"
PluginsInstalled[] = "UserId"
PluginsInstalled[] = "CustomPiwikJs"
PluginsInstalled[] = "WAIMatomoTheme"
@MATOMO_PLUGINS_INSTALLED@

[log]
log_writers[] = file
log_level = DEBUG
logger_file_path = /var/log/matomo/matomo.log

[WAIMatomoTheme]
waiUrl = "https://@MATOMO_WAI_URL@"

@MATOMO_MORE_CONFIG@
