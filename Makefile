PHP_BIN=$(shell which php)
PHPUNIT=vendor/bin/phpunit
TESTRUNNER=vendor/bin/testrunner

all:test

install:
	$(PHP_BIN) composer.phar install
	$(TESTRUNNER) "compile" --preload-script vendor/autoload.php

test:
	$(PHPUNIT) --tap --colors ./tests

testrunner:
	$(TESTRUNNER) "phpunit"  --preload-script ./vendor/autoload.php  --phpunit-config ./phpunit.xml --autotest ./tests ./src

compile:
	$(PHP_BIN) dbup compile