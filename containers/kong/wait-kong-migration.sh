#!/bin/sh
# wait-kong-migration.sh

set -e

host=`docker inspect -f "{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}" wai_postgres_1`

POSTGRES_USER="$1"
POSTGRES_PASSWORD="$2"
POSTGRES_DATABASE="$3"

until PGPASSWORD=$POSTGRES_PASSWORD psql -h $host -U $POSTGRES_USER -d $POSTGRES_DATABASE -c '\q'; do
  >&2 echo "Waiting for $POSTGRES_DATABASE database $POSTGRES_USER $POSTGRES_PASSWORD..."
  sleep 1
done

>&2 echo "$POSTGRES_DATABASE database is ready ..."