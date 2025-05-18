FROM php:8.3-fpm-alpine

ARG PUID=1000
ARG PGID=1000

RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    libzip-dev \
    oniguruma-dev \
    zlib-dev \
    libpq-dev \
  && docker-php-ext-install pdo_pgsql zip \
  && apk del .build-deps \
  && apk add --no-cache \
    libzip \
    libpq \
    curl \
    git \
    bash \
    shadow

RUN groupmod -o -g "${PGID}" www-data \
 && usermod -o -u "${PUID}" -g www-data www-data \
 && mkdir -p /var/www \
 && chown www-data:www-data /var/www

WORKDIR /var/www

COPY --chown=www-data:www-data . /var/www

COPY ./docker/php-fpm/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

USER www-data

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --optimize-autoloader

CMD ["/usr/local/bin/entrypoint"]