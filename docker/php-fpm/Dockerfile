FROM php:8.3-fpm-alpine

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

COPY ./docker/php-fpm/php.ini /usr/local/etc/php/conf.d/php.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . /var/www

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

RUN chown -R www-data:www-data /var/www

COPY ./docker/php-fpm/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

USER www-data

CMD ["/usr/local/bin/entrypoint"]