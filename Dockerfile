FROM php:8.3.8-fpm-alpine3.20

RUN apk add --no-cache wget postgresql-dev
# mysql-client msmtp perl wget procps shadow libzip libpng libjpeg-turbo libwebp freetype icu icu-data-full

RUN docker-php-ext-install pdo pdo_pgsql

# RUN apk add --no-cache --virtual build-essentials \
#     icu-dev icu-libs zlib-dev g++ make automake autoconf libzip-dev \
#     libpng-dev libwebp-dev libjpeg-turbo-dev freetype-dev && \
#     docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp && \
#     docker-php-ext-install gd && \
#     docker-php-ext-install mysqli && \
#     docker-php-ext-install pdo_mysql && \
#     docker-php-ext-install intl && \
#     docker-php-ext-install opcache && \
#     docker-php-ext-install exif && \
#     docker-php-ext-install zip && \
#     apk del build-essentials && rm -rf /usr/src/php*
    
RUN wget https://getcomposer.org/composer-stable.phar -O /usr/local/bin/composer && chmod +x /usr/local/bin/composer

ADD . /var/www/html
WORKDIR /var/www/html

RUN composer install --ignore-platform-reqs
