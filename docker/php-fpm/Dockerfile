FROM php:8.3-fpm-alpine

ARG PUID=1000
ARG PGID=1000

RUN set -eux; \
    apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        libzip-dev \
        oniguruma-dev \
        zlib-dev \
        libpq-dev \
    && docker-php-ext-install \
        pdo_pgsql \
        zip \
    && apk add --no-cache \
        libzip \
        libpq \
        curl \
        git \
        bash \
        shadow \
    && apk del .build-deps \
    \
    && groupmod -o -g "${PGID}" www-data \
    && usermod  -o -u "${PUID}" -g www-data www-data \
    \
    && mkdir -p /var/www \
    && chown www-data:www-data /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY --chown=www-data:www-data . /var/www

USER www-data

ENTRYPOINT ["docker-php-entrypoint"]
CMD ["php-fpm"]
