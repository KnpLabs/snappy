IMAGE_TAG:=knplabs/snappy:test

.PHONY: build
build:
	docker build ./ -t "${IMAGE_TAG}"

.PHONY: test
test: build
	$(MAKE) -C src/Bundle test IMAGE_TAG="${IMAGE_TAG}" ARGS="${ARGS}"

.PHONY: phpstan
phpstan:
	php vendor/bin/phpstan analyse --level max src/

.PHONY: php-cs-fixer
php-cs-fixer:
	tools/php-cs-fixer/vendor/bin/php-cs-fixer fix ./src
