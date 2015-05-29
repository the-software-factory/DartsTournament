#!/bin/bash
#
# Provisions the Vagrant box with Apache2, PHP and MySQL
#
echo "Provisioning virtual machine..."

set -e

export DEBIAN_FRONTEND=noninteractive

echo "Updating package definitions and installing base tools..."
apt-get update > /dev/null
apt-get install python-software-properties build-essential vim -y > /dev/null

echo "Installing Apache2..."
sudo apt-get install apache2 -y > /dev/null

echo "Installing MySQL..."
apt-get install debconf-utils -y > /dev/null
debconf-set-selections <<< "mysql-server mysql-server/root_password password password"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password password"
apt-get install mysql-server -y > /dev/null
update-rc.d mysql defaults

echo "Updating PHP repository..."
add-apt-repository ppa:ondrej/php5 -y > /dev/null

echo "Installing PHP..."
apt-get install php5-common php5-dev php5-cli php5-fpm libapache2-mod-php5 -y > /dev/null

echo "Installing PHP extensions..."
apt-get install php5-mcrypt php5-mysqlnd php5-curl php5-xdebug -y > /dev/null

echo "Adding custom .profile script to the home folder"
cp /var/www/.provision/.profile /home/vagrant/.profile

echo "Creating folder for code coverage report"
mkdir -p /home/vagrant/coverage
chown -R vagrant:vagrant /home/vagrant/coverage

echo "Creating schema and fixtures..."
mysql -uroot -ppassword < /var/www/.provision/db.sql

echo "Configuring vhost..."
sudo a2enmod rewrite
sudo rm /etc/apache2/sites-available/default
sudo cp /var/www/.provision/default-vhost /etc/apache2/sites-available/default

sudo service apache2 restart
echo "Provisioning complete!"
