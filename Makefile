# Run `make` (no arguments) to get a short description of what is available
# within this `Makefile`.

MKDOCS_IMAGE_ID := $(shell docker images -q laminas/mkdocs | xargs)
MDLINT_FILE = https://raw.githubusercontent.com/laminas/laminas-continuous-integration-action/refs/heads/1.43.x/setup/markdownlint/markdownlint.json

.PHONY: help build-mkdocs-image docs docs-lint install install-tools update bump bump-tools clean sa cs test composer-validate composer-require-checker qa

help: ## shows this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_\-\.]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

documentation-theme: ## fetch the documentation theme repo
	git clone git@github.com:laminas/documentation-theme.git

build-mkdocs-image: documentation-theme ## Build the mkdocs image with necessary dependencies for building the docs
	$(if ${MKDOCS_IMAGE_ID}, $(info Image already built), cd documentation-theme/builder && docker build -t laminas/mkdocs .)

docs: build-mkdocs-image ## build the docs using a Docker container
	docker run -it -w /app -v ${PWD}:/app --rm laminas/mkdocs ./documentation-theme/build.sh -u https://www.example.com
	$(info ${PWD}/docs/html/index.html)

docs-lint: .markdownlint.json ## Lint documentation
	docker run -it -w /app -v ${PWD}:/app --rm davidanson/markdownlint-cli2 "docs/**/*.md" "README.md"

.markdownlint.json: ## Fetch the most recent settings for Markdown lint
	curl -o .markdownlint.json ${MDLINT_FILE}

install: install-tools ## Install PHP dependencies
	composer install

install-tools: ## Install standalone dev tools
	cd tools/crc && composer install
	cd tools/rector && composer install

update: ## Update PHP dependencies
	composer update

bump: bump-tools ## Bump dev dependencies and update
	composer update && composer bump -D && composer update

bump-tools: ## Bump and update standalone dev tools
	cd tools/crc && composer update && composer bump -D && composer update
	cd tools/rector && composer update && composer bump -D && composer update

clean: ## Clear out caches and documentation assets
	rm -rf documentation-theme
	rm -rf docs/html
	$(if ${MKDOCS_IMAGE_ID}, docker image rm laminas/mkdocs, echo "Skip image removal" )
	rm -rf .phpunit.cache
	rm -f .phpcs-cache
	vendor/bin/psalm --clear-cache
	rm -f .markdownlint.json

sa: ## Run static analysis checks
	vendor/bin/psalm --no-cache --threads=1

cs: ## Run coding standards checks
	vendor/bin/phpcs

test: ## Run unit tests
	vendor/bin/phpunit

composer-validate: ## Validate composer.json and lock
	composer validate --strict

composer-require-checker: ## Check for symbols from un-declared dependencies
	tools/crc/vendor/bin/composer-require-checker check --config-file=tools/crc/config.json

rector: ## Run Rector and show the diff
	tools/rector/vendor/bin/rector process --dry-run -vv -c tools/rector/rector.php
.PHONY: rector

rector-ci: ## Run Rector and show the diff in GitHub format for CI
	tools/rector/vendor/bin/rector process --dry-run --output-format=github -vv -c tools/rector/rector.php
.PHONY: rector

rector-fix: ## Apply Rector changes
	tools/rector/vendor/bin/rector process -c tools/rector/rector.php
.PHONY: rector-fix

qa: composer-validate cs sa test composer-require-checker docs-lint ## Run all QA Checks
