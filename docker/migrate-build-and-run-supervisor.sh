#!/bin/bash
php /var/www/artisan migrate --force
php /var/www/artisan optimize
npm install
npm run build
/usr/bin/supervisord -u $1 -n -c $2
