#!/usr/bin/env bash

# link PHP config
sudo rm /etc/php.ini
sudo ln -s /web/conf/php.ini /etc/php.ini

# link nginx config
sudo rm /etc/nginx/php-fpm.conf
sudo ln -s /web/conf/php-fpm.conf /etc/nginx/php-fpm.conf

if [[ -f "/etc/nginx/conf.d/default.conf" ]]; then
	sudo mv /etc/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf_old
fi

sudo rm /etc/nginx/conf.d/threateye.conf
sudo ln -s /web/conf/threateye.conf /etc/nginx/conf.d/threateye.conf

sudo rm /etc/nginx/fastcgi.conf
sudo ln -s /web/conf/fastcgi.conf /etc/nginx/fastcgi.conf

# link prod config
sudo rm /web/src/threateyeback/environments/prod/common/config/main-local.php
sudo ln -s /web/conf/main-local.php /web/src/threateyeback/environments/prod/common/config/main-local.php

sudo rm /web/src/threateyeback/environments/prod/common/config/params-local.php
sudo ln -s /web/conf/params-local.php /web/src/threateyeback/environments/prod/common/config/params-local.php

# link dev config
sudo rm /web/src/threateyeback/environments/dev/common/config/main-local.php
sudo ln -s /web/conf/main-local.php /web/src/threateyeback/environments/dev/common/config/main-local.php

sudo rm /web/src/threateyeback/environments/dev/common/config/params-local.php
sudo ln -s /web/conf/params-local.php /web/src/threateyeback/environments/dev/common/config/params-local.php

# link test config
sudo rm /web/src/threateyeback/environments/test/common/config/main-local.php
sudo ln -s /web/conf/main-local.php /web/src/threateyeback/environments/test/common/config/main-local.php

sudo rm /web/src/threateyeback/environments/test/common/config/params-local.php
sudo ln -s /web/conf/params-local.php /web/src/threateyeback/environments/test/common/config/params-local.php

# create database
if [[ ! -f "/web/conf/db.conf" ]]; then
	mysql -u root -e "CREATE DATABASE IF NOT EXISTS threateye"
	mysql -u root -e "set password for 'root'@'localhost'=password('threateye*789')"
	echo "root@localhost" > /web/conf/db.conf
	echo "threateye*789" >> /web/conf/db.conf
fi

if [[ ! -d "/web/log" ]]; then
	mkdir /web/log
fi
if [[ ! -d "/web/static" ]]; then
	mkdir /web/static

fi
chmod 777 /web/log /web/static

sudo /web/src/threateyeback/init --env=Production --overwrite=a
sudo /web/src/threateyeback/yii migrate 0 y

sudo /web/src/threateyeback/yii task/set-machine-code

sudo systemctl restart nginx

sudo systemctl restart php-fpm.service
