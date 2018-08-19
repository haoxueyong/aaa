#!/usr/bin/env bash
#link mysql config
sudo rm /etc/my.cnf
sudo ln -s /web/conf/my.cnf /etc/my.cnf

#link php config
sudo rm /etc/php.ini
sudo ln -s /web/conf/php.ini /etc/php.ini
sudo rm /etc/php-cli.ini
sudo ln -s /web/conf/php-cli.ini /etc/php-cli.ini
sudo rm /usr/local/php/etc/php-fpm.conf
sudo ln -s /web/conf/php-fpm.conf /usr/local/php/etc/php-fpm.conf

# link redis config
sudo rm /usr/local/redis/etc/redis.conf
sudo ln -s /web/conf/redis.conf /usr/local/redis/etc/redis.conf


# link nginx config
sudo rm /usr/local/nginx/conf/nginx.conf
sudo ln -s /web/conf/nginx.conf /usr/local/nginx/conf/nginx.conf

# start servers
sudo /etc/init.d/mysqld start
sudo /usr/local/redis/bin/redis-server /usr/local/redis/etc/redis.conf
sudo /etc/init.d/php-fpm start
sudo /usr/local/nginx/sbin/nginx
# sudo service mongod restart


# set mysql privileges
mysql -u root -e "GRANT ALL PRIVILEGES ON *.* TO root@'192.168.1.%' IDENTIFIED BY ''; "

#create databases
mysql -u root -e "CREATE DATABASE IF NOT EXISTS iCatch"
