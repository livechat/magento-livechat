ARG phpVersion=8.4
FROM php:${phpVersion}-fpm

USER root

RUN apt-get update \
	&& apt-get install -y --no-install-recommends git unzip default-mysql-client \
	&& rm -rf /var/lib/apt/lists/*

COPY --from=ghcr.io/mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions bcmath gd intl opcache pdo_mysql soap sockets xsl zip sodium

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY php.ini /usr/local/etc/php/conf.d/zz-magento.ini

# Created before the volume is mounted so the named volume inherits www-data ownership
RUN mkdir -p /var/www/html /var/www/.composer && chown -R www-data:www-data /var/www/html /var/www/.composer

ENV COMPOSER_HOME=/var/www/.composer

USER www-data

WORKDIR /var/www/html
