FROM php:7.4-cli-alpine

RUN docker-php-ext-install pdo pdo_mysql

RUN apk add --update --no-cache --virtual .build-dependencies $PHPIZE_DEPS \
        && docker-php-ext-install pcntl sockets \
        && pecl install xdebug-3.1.6 \
        && docker-php-ext-enable xdebug \
        && pecl clear-cache \
        && apk del .build-dependencies
