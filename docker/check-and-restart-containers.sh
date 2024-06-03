#!/bin/bash

declare -a containers=("php" "nginx" "db" "cron" "queue" "redis")
for container in "${containers[@]}"
do
   if [ "$(docker ps -q -f name="$container")" == "" ]; then
        echo "Container $container is not running. Restarting Docker Compose setup..."
        docker compose -f /var/www/docker-compose-prod.yml down
        docker compose -f /var/www/docker-compose-prod.yml up -d nginx
        exit 0
   fi
done

echo "All containers are running."
