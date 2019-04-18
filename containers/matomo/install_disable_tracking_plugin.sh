#!/bin/sh
mysql -u root -p@DB_PASSWORD@ < /opt/disable_tracking_plugin.sql
rm /opt/disable_tracking_plugin.sql
rm /opt/install_disable_tracking_plugin.sh
