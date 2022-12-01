#!/bin/sh
sudo sed -i '/zend_extension/s/^;//g' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
sudo service apache2 reload