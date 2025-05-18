#!/usr/bin/env bash

set -e

role=${CONTAINER_ROLE:-app}

if [ "$role" = "app" ]; then
    echo "App role"

    until php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
      sleep 1
    done

    php bin/console doctrine:migrations:migrate --no-interaction

    exec php-fpm
elif [ "$role" = "queue" ]; then
    echo "Queue role"

   until php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
      sleep 1
    done

    php bin/console messenger:consume async --no-interaction --memory-limit=512M
else
    echo "Could not match the container role \"$role\""
    exit 1
fi
