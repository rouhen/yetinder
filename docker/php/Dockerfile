FROM php:8.3-fpm-alpine

RUN apk add --no-cache postgresql-dev

RUN docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html
