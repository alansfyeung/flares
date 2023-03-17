FROM php:5.6-apache

RUN apt-get update && apt-get install --yes cron g++ gettext libicu-dev openssl libc-client-dev libkrb5-dev  libxml2-dev libfreetype6-dev libgd-dev libmcrypt-dev bzip2 libbz2-dev libtidy-dev libcurl4-openssl-dev libz-dev libmemcached-dev libxslt-dev git zip unzip

RUN a2enmod rewrite

RUN docker-php-ext-install pdo pdo_mysql mysql 
RUN docker-php-ext-enable pdo pdo_mysql mysql

RUN docker-php-ext-configure gd --with-freetype-dir=/usr --with-jpeg-dir=/usr --with-png-dir=/usr
RUN docker-php-ext-install gd

RUN docker-php-ext-install zip
RUN docker-php-ext-enable zip

RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
RUN php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

COPY ./ /var/www/html/
WORKDIR /var/www/html
RUN chmod -R a+rwx ./storage ./bootstrap/cache
RUN composer install

RUN cp .env.example .env
RUN php artisan key:generate
RUN sed -ri -e 's#DB_HOST=.*$#DB_HOST=host.docker.internal#' .env
RUN sed -ri -e 's#DB_DATABASE=.*$#DB_DATABASE=flares_dev#' .env
RUN sed -ri -e 's#DB_USERNAME=.*$#DB_USERNAME=flares_dev#' .env
RUN sed -ri -e 's#DB_PASSWORD=.*$#DB_PASSWORD=flares_dev#' .env

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's#/var/www/html#${APACHE_DOCUMENT_ROOT}#g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's#/var/www/html#${APACHE_DOCUMENT_ROOT}#g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

