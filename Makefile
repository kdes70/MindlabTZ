#!/usr/bin/make
SHELL = /bin/sh

export PWD := $(PWD)

ifneq (,$(wildcard ./.env))
    include .env
    export
endif

docker_bin := $(shell command -v docker 2> /dev/null)
# Путь к docker-compose или docker compose в зависимости от наличия
docker_compose_bin := $(shell command -v docker-compose 2> /dev/null)

ifndef docker_compose_bin
	docker_compose_bin := docker compose
endif

PHP_CONTAINER_NAME := php

APP_CONTAINER_RUN := $(docker_compose_bin) run --rm $(PHP_CONTAINER_NAME)

install: docker-down-clear docker-build vendor-install db-init app-reset

docker-up: memory
	$(docker_compose_bin) up -d

docker-down:
	$(docker_compose_bin) down

docker-down-clear:
	$(docker_compose_bin) down -v --remove-orphans

docker-build: memory
	$(docker_compose_bin) up --build -d

# DataBase
db-init:
	echo "Waiting for db..."
	sleep 5
	make db-fresh
	make db-seeds

db-fresh:
	$(APP_CONTAINER_RUN) php artisan migrate:fresh

db-migrations:
	$(APP_CONTAINER_RUN) php artisan migrate --force

db-seeds:
	$(APP_CONTAINER_RUN) php artisan db:seed

app-reset:
	$(APP_CONTAINER_RUN) php artisan key:generate
	$(APP_CONTAINER_RUN) php artisan config:clear
	$(APP_CONTAINER_RUN) php artisan route:clear
	$(APP_CONTAINER_RUN) php artisan cache:clear

vendor-install:
	$(docker_compose_bin) exec -u www-data $(PHP_CONTAINER_NAME) composer install

shell: docker-up
	$(docker_compose_bin) exec -it $(PHP_CONTAINER_NAME) sh

memory:
	sudo sysctl -w vm.max_map_count=262144

perm:
	echo "Setting permissions... $(USER)"
	sudo chmod -R 775 backend/storage backend/bootstrap/cache frontend
	sudo chown -R $(USER):www-data backend/storage backend/bootstrap/cache frontend
	setfacl -Rdm u:$(USER):rwx,g:www-data:rwx,o::r-x backend/storage backend/bootstrap/cache