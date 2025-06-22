FROM php:8.3-fpm-alpine

# Doinstalovat knihovny potřebné pro pdo_pgsql
RUN apk add --no-cache postgresql-dev

# Instalace PHP rozšíření
RUN docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html

# Composer (volitelně)
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer
