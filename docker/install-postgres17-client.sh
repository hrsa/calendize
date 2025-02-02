#!/bin/bash
set -e

CONTAINER_NAME=$1

if [ -z "$CONTAINER_NAME" ]; then
    echo "Error: You must specify the container name as an argument."
    echo "Usage: $0 <container_name>"
    exit 1
fi

docker exec -u root $CONTAINER_NAME bash -c "apt-get update && apt-get install -y gnupg2 wget lsb-release"

docker exec -u root $CONTAINER_NAME bash -c "wget --quiet -O /usr/share/keyrings/postgresql.asc https://www.postgresql.org/media/keys/ACCC4CF8.asc"

docker exec -u root $CONTAINER_NAME bash -c "echo \"deb [signed-by=/usr/share/keyrings/postgresql.asc] http://apt.postgresql.org/pub/repos/apt \$(lsb_release -cs)-pgdg main\" > /etc/apt/sources.list.d/pgdg.list"

docker exec -u root $CONTAINER_NAME bash -c "apt-get update && apt-get install -y postgresql-client-17"

docker exec -u root $CONTAINER_NAME bash -c "apt-get clean && rm -rf /var/lib/apt/lists/*"

