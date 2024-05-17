#!/bin/bash
composer dump-autoload
if [ "$3" = "migrate-and-build" ]; then
    php /var/www/artisan migrate --force
    npm install
    npm run build
fi
php /var/www/artisan optimize
/usr/bin/supervisord -u "$1" -n -c "$2"
