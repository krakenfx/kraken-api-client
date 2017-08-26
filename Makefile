.PHONY: composer cs phpstan

dist: composer cs phpstan

composer:
	composer validate

cs:
	vendor/bin/php-cs-fixer fix -vvv --diff

phpstan:
	vendor/bin/phpstan analyse . --level 7 --configuration phpstan.neon
