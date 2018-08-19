
# install base build package
sudo yum install -y gcc gcc-c++ pcre-devel zlib-devel libxml2-devel libcurl-devel libjpeg-devel libpng-devel  freetype-devel openssl-devel git



wget http://jaist.dl.sourceforge.net/project/mcrypt/Libmcrypt/2.5.8/libmcrypt-2.5.8.tar.gz
tar zxvf libmcrypt-2.5.8.tar.gz
cd libmcrypt-2.5.8
./configure
make
sudo make install

# install mysql
sudo yum install -y mysql mysql-server mysql-libs




# install PHP
## 安装epel-release
sudo rpm -ivh http://dl.fedoraproject.org/pub/epel/7/x86_64/e/epel-release-7-5.noarch.rpm
sudo yum install epel-release
## 安装PHP7
sudo rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm
sudo yum install php71w php71w-common php71w-mysql php71w-pdo php71w-mbstring php71w-gd php71w-fpm php71w-mcrypt



# install ImageMagick
# sudo yum install -y ImageMagick ImageMagick-devel
# sudo /usr/local/php/bin/pecl update-channels
# sudo /usr/local/php/bin/pecl install imagick



# install pthreads
# sudo /usr/local/php/bin/pecl install pthreads


# # install poppler-utils(PDF)
# sudo yum install -y poppler-utils



# # install texlive-utils (pdfcrop)
# sudo yum install -y texlive-utils



# install nginx
sudo yum -y install make zlib zlib-devel gcc-c++ libtool  openssl openssl-devel

wget http://downloads.sourceforge.net/project/pcre/pcre/8.35/pcre-8.35.tar.gz
tar zxvf pcre-8.35.tar.gz
cd pcre-8.35
./configure
make && make install

wget http://nginx.org/download/nginx-1.10.2.tar.gz
tar -zxvf nginx-1.10.2.tar.gz

cd nginx-1.10.2

./configure --prefix=/usr/local/nginx --with-http_stub_status_module --with-http_ssl_module --with-pcre=../pcre-8.35
make && make install

sudo ln -s /usr/local/nginx/sbin/nginx /usr/bin/nginx


# install redis 
sudo rpm -i tcl-8.5.7-6.el6.x86_64.rpm


# install redis 
make

make test 

sudo make PREFIX=/usr/local/redis install

sudo mkdir -p /usr/local/redis/etc

sudo cp redis.conf /usr/local/redis/etc/redis.conf


