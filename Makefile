init: docker-down-clear docker-pull docker-build docker-up
up: docker-up
build: docker-build
down: docker-down

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

bash:
	docker compose exec php-fpm bash
