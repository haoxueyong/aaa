# link nginx.conf
sudo rm -rf /etc/nginx/https_pems
sudo mkdir /etc/nginx/https_pems
sudo cp /vagrant/https_pems/server.crt /etc/nginx/https_pems/server.crt
sudo cp /vagrant/https_pems/server.key /etc/nginx/https_pems/server.key

sudo rm -f /etc/nginx/conf.d/iconnect.conf
sudo cp /vagrant/srv/dev/iconnect.conf /etc/nginx/conf.d/iconnect.conf
sudo systemctl restart nginx

# set mysql privileges
mysql -u root -e "CREATE USER IF NOT EXISTS 'root'@'192.168.1.%' IDENTIFIED BY '';"
mysql -u root -e "GRANT ALL PRIVILEGES ON *.* TO root@'192.168.1.%' IDENTIFIED BY ''; "

#create databases
mysql -u root -e "CREATE DATABASE IF NOT EXISTS iconnect"

#init and migrate
php /vagrant/src/init --env=Development --overwrite=a
php /vagrant/src/yii migrate  0 y

sudo /vagrant/src/yii task/set-machine-code