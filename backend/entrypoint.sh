#!/bin/sh
composer install --no-interaction --prefer-dist --optimize-autoloader
composer require --dev symfony/browser-kit
php-fpm
