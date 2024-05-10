#!/bin/bash

docker compose -f /var/www/docker-compose.yml run --rm certbot
docker compose -f /var/www/docker-compose.yml down
docker system prune -a -f
docker compose -f /var/www/docker-compose.yml up -d nginx
