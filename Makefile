.PHONY: build
build:
	docker compose build php

.PHONY: composer
composer: build
	docker compose run --rm -u $$(id -u) php composer update

.PHONY: php-cs-fixer
php-cs-fixer: composer
	docker compose run --rm -u $$(id -u) -e PHP_CS_FIXER_IGNORE_ENV=1 php vendor/bin/php-cs-fixer fix --diff -vvv

.PHONY: rector
rector: composer
	docker compose run --rm -u $$(id -u) php vendor/bin/rector

.PHONY: phpunit
phpunit: composer
	docker compose run --rm -u $$(id -u) php vendor/bin/phpunit

.PHONY: phpstan
phpstan: composer
	docker compose run --rm -u $$(id -u) php vendor/bin/phpstan
