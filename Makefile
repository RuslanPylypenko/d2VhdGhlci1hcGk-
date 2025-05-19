init: docker-down-clear docker-pull docker-build docker-up composer-install
up: docker-up
build: docker-build
down: docker-down
check: validate

docker-up:
	docker compose up -d

docker-build:
	docker compose build

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker compose down -v --remove-orphans

docker-pull:
	docker compose pull

composer-install:
	docker compose run --rm php-fpm composer install

bash:
	docker compose exec php-fpm bash

queue-run:
	docker compose exec php-fpm php bin/console messenger:consume async

validate:
	docker compose run --rm php-fpm composer run validate

fix-style:
	docker compose run --rm php-fpm composer run fix-style

include .env.local
export $(shell sed 's/=.*//' .env)

build-app:
	docker build --platform linux/amd64 --pull --file=./docker/nginx/Dockerfile --tag=${REGISTRY}/weather-app-nginx:${IMAGE_TAG} .
	docker build --platform linux/amd64 --pull --file=./docker/php-fpm/Dockerfile --tag=${REGISTRY}/weather-app-php-fpm:${IMAGE_TAG} .

push-app:
	docker push ${REGISTRY}/weather-app-nginx:${IMAGE_TAG}
	docker push ${REGISTRY}/weather-app-php-fpm:${IMAGE_TAG}

SSH := ssh -i ~/.ssh/digital_ocean -p ${PORT} ${USER}@${HOST}
SCP := scp -i ~/.ssh/digital_ocean -P ${PORT}

deploy-app:
	${SSH} 'rm -rf /var/www/weather_app_${BUILD_NUMBER}'
	${SSH} 'mkdir /var/www/weather_app_${BUILD_NUMBER}'
	${SCP} compose-production.yml ${USER}@${HOST}:/var/www/weather_app_${BUILD_NUMBER}/docker-compose-production.yml
	${SCP} compose-production.yml ${USER}@${HOST}:/var/www/weather_app_${BUILD_NUMBER}/docker-compose-production.yml
	${SSH} 'cd /var/www/weather_app_${BUILD_NUMBER} && echo "COMPOSE_PROJECT_NAME=weather_app" >> .env'
	${SSH} 'cd /var/www/weather_app_${BUILD_NUMBER} && echo "REGISTRY=${REGISTRY}" >> .env'
	${SSH} 'cd /var/www/weather_app_${BUILD_NUMBER} && echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	${SSH} 'cd /var/www/weather_app_${BUILD_NUMBER} && docker-compose -f docker-compose-production.yml pull'
	${SSH} 'cd /var/www/weather_app_${BUILD_NUMBER} && docker-compose -f docker-compose-production.yml up --build --remove-orphans -d'
	${SSH} 'rm -rf /var/www/weather_app'
	${SSH} 'ln -sr /var/www/weather_app_${BUILD_NUMBER} /var/www/weather_app'
