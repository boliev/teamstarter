FROM php:7.2-fpm
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql opcache
RUN pecl install xdebug-2.6.0 && docker-php-ext-enable xdebug
RUN pecl install apcu && docker-php-ext-enable apcu