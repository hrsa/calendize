#!/bin/bash

docker compose -f /var/www/docker-compose-prod.yml run --rm certbot
docker compose -f /var/www/docker-compose-prod.yml down
docker system prune -a -f
docker compose -f /var/www/docker-compose-prod.yml up -d nginx
