IMAGE_TAG:=knplabs/snappy:test

.PHONY: build
build:
	docker build ./ -t "${IMAGE_TAG}"

.PHONY: test
test: build
	$(MAKE) -C src/Bundle test IMAGE_TAG="${IMAGE_TAG}" ARGS="${ARGS}"
