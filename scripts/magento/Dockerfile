ARG phpVersion=8.4
FROM php:${phpVersion}-fpm AS base

USER root

RUN apt-get update \
	&& apt-get install -y --no-install-recommends git unzip default-mysql-client \
	&& rm -rf /var/lib/apt/lists/*

COPY --from=ghcr.io/mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions bcmath gd intl opcache pdo_mysql soap sockets xsl zip sodium ftp

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY php.ini /usr/local/etc/php/conf.d/zz-magento.ini

# Created before the volume is mounted so the named volume inherits www-data ownership
RUN mkdir -p /var/www/html /var/www/.composer && chown -R www-data:www-data /var/www/html /var/www/.composer

ENV COMPOSER_HOME=/var/www/.composer

USER www-data

WORKDIR /var/www/html

FROM base AS magento

ARG magentoVersion

# Auth comes from a BuildKit secret so the keys never land in an image layer.
# Advisory blocking is off: this is a throwaway dev image and older Magento releases carry advisories.
RUN --mount=type=secret,id=composer_auth,target=/tmp/composer_auth.json,uid=33 \
	COMPOSER_AUTH="$(cat /tmp/composer_auth.json)" COMPOSER_MEMORY_LIMIT=-1 COMPOSER_NO_SECURITY_BLOCKING=1 \
	composer create-project --no-interaction --no-progress \
	--repository-url=https://repo.magento.com/ \
	magento/project-community-edition:${magentoVersion} /var/www/html \
	&& rm -rf /var/www/.composer/cache

# Mount point for the module (www-data owned so Magento can write next to it)
RUN mkdir -p app/code/LiveChat/LiveChat
