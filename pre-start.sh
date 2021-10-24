#!/bin/sh

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan queue:restart

php-fpm
