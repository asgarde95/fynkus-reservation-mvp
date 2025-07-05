#!/bin/sh
composer install --no-interaction --prefer-dist --optimize-autoloader
php-fpm
