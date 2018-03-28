#!/bin/sh
mysql -u root -p@DB_PASSWORD@ < /opt/matomo.sql
mysql -u root -p@DB_PASSWORD@ matomo -e 'UPDATE user SET login = "@MATOMO_ROOT_USER@" WHERE superuser_access = 1;'
mysql -u root -p@DB_PASSWORD@ matomo -e 'UPDATE user SET password = "@MATOMO_ROOT_PASSWORD@" WHERE superuser_access = 1;'
mysql -u root -p@DB_PASSWORD@ matomo -e 'UPDATE user SET token_auth = "@MATOMO_ROOT_APIKEY@" WHERE superuser_access = 1;'
rm /opt/matomo.sql
rm /opt/install_matomo_db.sh
rm /opt/*.html
