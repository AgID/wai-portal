#!/bin/sh
mysql -u root -p@DB_PASSWORD@ < /opt/matomo-install/disable_tracking_plugin.sql
