.PHONY: test
test:
	docker run --rm -i -w /src/Bundle -v "$(shell pwd)/../:/src" "${IMAGE_TAG}" vendor/bin/phpunit $(ARGS) -v
