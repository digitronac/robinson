#!/bin/bash
php ../../../vendor/bin/phpunit --colors
php ../../../vendor/bin/phpcs --standard=RAS --ignore="../logs" ../*
