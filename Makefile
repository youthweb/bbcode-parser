DOCKER := docker

PHP_VERSION ?= 7.4

IMAGE := bbcode-parser-php
TAG := $(IMAGE):$(PHP_VERSION)

DOCKERFILE_DIR := scripts/docker/php
WORKDIR := /app
DOCKER_RUN_ARGS := run --rm --user "$(shell id -u):$(shell id -g)" --env "HOME=/tmp" --volume "$(CURDIR):$(WORKDIR)" --workdir "$(WORKDIR)"
DOCKER_RUN := $(DOCKER) $(DOCKER_RUN_ARGS)

.PHONY: build check-docker clean codestyle composer coverage install phpstan phpunit shell test

.DEFAULT_GOAL := test

check-docker:
	@command -v docker >/dev/null 2>&1 || { echo "error: Docker is required for this target. Install Docker, then run the tools inside the dev container." >&2; exit 1; }

build: check-docker
	docker build --file "$(DOCKERFILE_DIR)/Dockerfile" --build-arg "PHP_VERSION=$(PHP_VERSION)" --tag "$(TAG)" .

install: build
	$(DOCKER_RUN) "$(TAG)" composer install

composer: build
	$(DOCKER_RUN) "$(TAG)" composer $(ARGS)

shell: build
	$(DOCKER_RUN) "$(TAG)" bash

phpunit: build
	$(DOCKER_RUN) "$(TAG)" vendor/bin/phpunit

phpstan: build
	$(DOCKER_RUN) "$(TAG)" composer phpstan

codestyle: build
	$(DOCKER_RUN) "$(TAG)" composer php-cs-fixer

coverage: build
	$(DOCKER_RUN) --env "XDEBUG_MODE=coverage" "$(TAG)" vendor/bin/phpunit --coverage-html="build/code-coverage"

test: build
	$(DOCKER_RUN) "$(TAG)" sh -c "if [ ! -f vendor/autoload.php ]; then composer install; fi; vendor/bin/phpunit"

clean:
	rm -rf build vendor composer.lock .php-cs-fixer.cache .phpunit.result.cache
