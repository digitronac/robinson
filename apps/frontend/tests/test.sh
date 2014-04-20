#!/bin/bash
php ../../../vendor/bin/phpunit --colors --coverage-clover clover.xml
php ../../../vendor/bin/phpcs --standard=PSR2 --ignore="../tests" ../*
php coverage-checker.php clover.xml 80
