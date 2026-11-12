ARG phpVersion=8.1
FROM php:${phpVersion}-cli-alpine

USER root

RUN apk add --no-cache git unzip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV COMPOSER_HOME=/tmp/composer

WORKDIR /home/www-data

CMD [ "composer" ]
