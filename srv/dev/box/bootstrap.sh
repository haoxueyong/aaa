#!/usr/bin/env bash
dl_if_none() {
# takes 2 parameters
# $1 - filename
# $2 - url
echo Downloading source file
if [ -e "$1" ]
then
echo Source file $1 already exists
else
wget $2 -O "$1"
fi
} 


# install base build package
sudo yum install -y gcc gcc-c++ pcre-devel zlib-devel libxml2-devel libcurl-devel libjpeg-devel libpng-devel  freetype-devel openssl-devel git

# make install directory
mkdir /install

# install mysql
sudo yum install -y mysql mysql-server mysql-libs
#sudo rm /etc/my.cnf
#sudo ln -s /vagrant/srv/dev/my.cnf /etc/my.cnf
#sudo /etc/init.d/mysql start

# install mongodb
# sudo $(echo "[mongodb]
# name=MongoDB Repository
# baseurl=http://downloads-distro.mongodb.org/repo/redhat/os/x86_64/
# gpgcheck=0
# enabled=1" > /etc/yum.repos.d/mongodb.repo)
# sudo yum install -y mongodb-org

# install php
cd /install
wget http://jaist.dl.sourceforge.net/project/mcrypt/Libmcrypt/2.5.8/libmcrypt-2.5.8.tar.gz
tar zxvf libmcrypt-2.5.8.tar.gz
cd libmcrypt-2.5.8
./configure
make
if [ $? -ne 0 ]; then
  exit
fi
sudo make install

cd /install
wget http://jp2.php.net/distributions/php-5.6.10.tar.bz2
tar jxvf php-5.6.10.tar.bz2
cd php-5.6.10
./configure --prefix=/usr/local/php --with-config-file-path=/etc/ --with-config-file-scan-dir=/etc/php.d --with-mysql=mysqlnd --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd --with-zlib --enable-mbstring --with-gd --with-jpeg-dir --with-png-dir --enable-gd-native-ttf --enable-fpm --with-curl --with-mcrypt --with-freetype-dir --enable-pcntl --with-openssl --enable-zip
make 
if [ $? -ne 0 ]; then
  exit
fi
make install
sudo cp sapi/fpm/init.d.php-fpm /etc/init.d/php-fpm
sudo chmod +x /etc/init.d/php-fpm
#sudo rm /etc/php.ini
#sudo ln -s /vagrant/srv/dev/php.ini /etc/php.ini
#sudo rm /usr/local/php/etc/php-fpm.conf
#sudo ln -s /vagrant/srv/dev/php-fpm.conf /usr/local/php/etc/php-fpm.conf
#sudo /etc/init.d/php-fpm start

# install mongo extension
# yes no | sudo /usr/local/php/bin/pecl install mongo

#install imagemagick
sudo yum install -y ImageMagick ImageMagick-devel
sudo /usr/local/php/bin/pecl update-channels
yes "" | sudo /usr/local/php/bin/pecl install imagick
if [ $? -ne 0 ]; then
  exit
fi

# install poppler-utils (pdftops)
sudo yum install -y poppler-utils
if [ $? -ne 0 ]; then
  exit
fi

# install texlive-utils (pdfcrop)
sudo yum install -y texlive-utils
if [ $? -ne 0 ]; then
  exit
fi

# install nginx
cd /install
wget http://nginx.org/download/nginx-1.8.0.tar.gz
tar zxvf nginx-1.8.0.tar.gz 
cd nginx-1.8.0
./configure
make
if [ $? -ne 0 ]; then
  exit
fi
sudo make install
#sudo rm /usr/local/nginx/conf/nginx.conf
#sudo ln -s /vagrant/srv/dev/nginx.conf /usr/local/nginx/conf/nginx.conf

# remove temp files
sudo rm -rf /install
sudo yum clean headers
sudo yum clean packages


# install composer
sudo curl -sS https://getcomposer.org/installer | sudo /usr/local/php/bin/php -- --install-dir=/usr/bin
#start servers
#sudo /etc/init.d/mysqld start
#sudo /etc/init.d/php-fpm start
#sudo /usr/local/nginx/sbin/nginx
# chkconfig --add mysqld
# chkconfig --add php-fpm
# echo "/usr/local/nginx/sbin/nginx" >> /etc/rc.d/rc.local
