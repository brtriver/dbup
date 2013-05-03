PHP_BIN=$(shell which php)
CURL_BIN=$(shell which curl)
PHPUNIT=phpunit.phar

all:test

setup:
	$(PHP_BIN) -r "eval('?>'.file_get_contents('https://getcomposer.org/installer'));"
	$(CURL_BIN) -SsLO http://pear.phpunit.de/get/phpunit.phar

install:
	$(PHP_BIN) composer.phar install

test:
	$(PHP_BIN) $(PHPUNIT) --tap --colors ./tests

testrunner:
	guard -i

compile:
	$(PHP_BIN) dbup compile
