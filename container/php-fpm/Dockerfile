FROM php:7.2-fpm-alpine

RUN apk --no-cache add \
            php7-memcached \
            curl
RUN docker-php-ext-enable /usr/lib/php7/modules/memcached.so

RUN curl --silent --show-error https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
