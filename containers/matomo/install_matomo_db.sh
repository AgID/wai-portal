#!/bin/sh

hashed_password=`php7 -r "echo password_hash(md5('root'), PASSWORD_BCRYPT) . PHP_EOL;"`
mysql -u root -p@DB_PASSWORD@ < /opt/matomo.sql
mysql -u root -p@DB_PASSWORD@ matomo -e "UPDATE `user` SET `login` = '@MATOMO_ROOT_USER@' WHERE superuser_access = 1;"
mysql -u root -p@DB_PASSWORD@ matomo -e "UPDATE `user` SET `password` = '$hashed_password' WHERE superuser_access = 1;"
mysql -u root -p@DB_PASSWORD@ matomo -e "UPDATE `user` SET `token_auth` = '@MATOMO_ROOT_APIKEY@' WHERE superuser_access = 1;"
rm /opt/matomo.sql
rm /opt/install_matomo_db.sh
rm /opt/*.html
