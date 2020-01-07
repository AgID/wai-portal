#!/bin/sh
mysql -u root -p@DB_PASSWORD@ < /opt/matomo-install/matomo.sql
mysql -u root -p@DB_PASSWORD@ -e 'CREATE USER IF NOT EXISTS "@MATOMO_DB_USER@"@"%" IDENTIFIED BY "@MATOMO_DB_PASSWORD@";'
mysql -u root -p@DB_PASSWORD@ -e 'GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES ON matomo.* TO "@MATOMO_DB_USER@"@"%";'
mysql -u root -p@DB_PASSWORD@ -e 'GRANT FILE ON *.* TO "@MATOMO_DB_USER@"@"%";'
mysql -u root -p@DB_PASSWORD@ matomo -e 'UPDATE user SET login = "@MATOMO_ROOT_USER@" WHERE superuser_access = 1;'
mysql -u root -p@DB_PASSWORD@ matomo -e 'UPDATE user SET password = "@MATOMO_ROOT_PASSWORD@" WHERE superuser_access = 1;'
mysql -u root -p@DB_PASSWORD@ matomo -e 'UPDATE user SET token_auth = "@MATOMO_ROOT_APIKEY@" WHERE superuser_access = 1;'
