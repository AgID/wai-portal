#!/bin/sh
# wait-for-postgres.sh

set -e
  
host=`docker inspect -f "{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}" wai_postgres_1`
POSTGRES_USER="$1"
POSTGRES_PASSWORD="$2"

until PGPASSWORD=$POSTGRES_PASSWORD psql -h $host -U $POSTGRES_USER -d template1 -c '\q'; do
  >&2 echo "Waiting for wai_postgres_1 to start ..."
  sleep 1
done
  
>&2 echo "wai_postgres_1 is up ..."