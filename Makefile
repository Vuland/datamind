.PHONY: help init start stop ssh

COMPOSE = docker compose -f docker/docker-compose.yml

help:
	@echo "Targets:"
	@echo "  init             Build image + composer install"
	@echo "  start            Start Mongo + Elasticsearch"
	@echo "  stop             Stop containers"
	@echo "  ssh              Enter app container shell"

init:
	$(COMPOSE) build
	$(COMPOSE) run --rm app composer install

start:
	$(COMPOSE) up -d

stop:
	$(COMPOSE) down

ssh:
	$(COMPOSE) exec app bash

