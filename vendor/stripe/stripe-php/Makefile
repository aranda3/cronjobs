# NOTE: this file is deprecated and slated for deletion; prefer using the equivalent `just` commands.

export PHPDOCUMENTOR_VERSION := v3.0.0

vendor: composer.json
	composer install

vendor/bin/phpdoc: vendor
	curl -sfL https://github.com/phpDocumentor/phpDocumentor/releases/download/$(PHPDOCUMENTOR_VERSION)/phpDocumentor.phar -o vendor/bin/phpdoc
	chmod +x vendor/bin/phpdoc

test: vendor
	vendor/bin/phpunit
.PHONY: test

ci-test: vendor
	echo "calling build with $$AUTOLOAD"
	./build.php $$AUTOLOAD
.PHONY: ci-test

fmt: vendor
	PHP_CS_FIXER_IGNORE_ENV=true vendor/bin/php-cs-fixer fix -v --using-cache=no
.PHONY: fmt

fmtcheck: vendor
	PHP_CS_FIXER_IGNORE_ENV=true vendor/bin/php-cs-fixer fix -v --dry-run --using-cache=no
.PHONY: fmtcheck

phpdoc: vendor/bin/phpdoc
	vendor/bin/phpdoc

phpstan: vendor
	php -d memory_limit=512M vendor/bin/phpstan analyse lib tests
.PHONY: phpstan

phpstan-baseline: vendor/bin/phpstan
	php -d memory_limit=512M vendor/bin/phpstan analyse lib tests --generate-baseline
.PHONY: phpstan-baseline

update-version:
	@echo "$(VERSION)" > VERSION
	@perl -pi -e 's|VERSION = '\''[.\-\w\d]+'\''|VERSION = '\''$(VERSION)'\''|' lib/Stripe.php
.PHONY: update-version

codegen-format: fmt
.PHONY: codegen-format
