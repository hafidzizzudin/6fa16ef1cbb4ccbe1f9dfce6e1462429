FROM php:8.3.8-fpm-alpine3.20

RUN apk add --no-cache wget postgresql-dev nano

RUN docker-php-ext-install pdo pdo_pgsql

RUN wget https://getcomposer.org/composer-stable.phar -O /usr/local/bin/composer && chmod +x /usr/local/bin/composer

ADD . /var/www/html
WORKDIR /var/www/html

RUN composer install
