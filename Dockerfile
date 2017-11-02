FROM php:7.1-fpm
MAINTAINER Miha <finder_ausberlin@yahoo.de> version 0.2

RUN apt-get update
RUN apt-get install -y zlib1g-dev
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install sockets
RUN docker-php-ext-install pcntl
RUN docker-php-ext-install zip
RUN pecl install xdebug-2.5.5
RUN echo "zend_extension=xdebug.so" >> /usr/local/etc/php/conf.d/xdebug.ini
RUN apt-get install -y git
RUN apt-get install -y zip unzip

RUN php -r "copy('https://getcomposer.org/download/1.3.1/composer.phar', '/usr/local/bin/composer');" && \
    chmod +x /usr/local/bin/composer

ADD . /var/www

WORKDIR /var/www
RUN composer install
