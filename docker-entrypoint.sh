#!/bin/sh
set -e

php artisan config:cache
php artisan route:cache || true
php artisan view:cache || true
php artisan storage:link || true
php artisan migrate --force

exec apache2-foreground
