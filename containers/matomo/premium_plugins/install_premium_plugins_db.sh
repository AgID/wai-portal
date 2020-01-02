#!/bin/sh

usage() {
    echo "Usage: $0 -p|--public-user <string> -i|--rollup-id <integer> -u|--db-user <string> -l|--db-password <string> -d|--database <string>" 1>&2; exit 1;
}

OPTIONS=$(getopt -n "$0" -o p:i:u:l:d: --long "public-user:,rollup-id:,db-user:,db-password:,database:"  -- "$@")

eval set -- "$OPTIONS"

while true; do
    case "$1" in
        -p|--public-user)
            PUBLIC_USER=$2
            ;;
        -i|--rollup-id)
            ROLLUP_ID=$2
            ;;
        -u|--db-user)
            DB_USER=$2
            ;;
        -l|--db-password)
            DB_PASSWORD=$2
            ;;
        -d|--database)
            DB=$2
            ;;
        --)
            shift
            break
            ;;
        ?)
            usage
            ;;
    esac
    shift 2
done

find -P /tmp/premium_plugins/* -prune -type d -printf "%f\n" | while IFS= read -r directory; do
    # Add plugin SQL dump into matomo database
    if [ -f "/tmp/premium_plugins/$directory/db_dump.sql" ]; then
        sed -i -e s/@MATOMO_PUBLIC_USER@/"$PUBLIC_USER"/g "/tmp/premium_plugins/$directory/db_dump.sql"
        sed -i -e s/@MATOMO_PUBLIC_ROLLUP_ID@/"$ROLLUP_ID"/g "/tmp/premium_plugins/$directory/db_dump.sql"
        mysql -u "$DB_USER" -p"${DB_PASSWORD}" "${DB}"< "/tmp/premium_plugins/$directory/db_dump.sql"
    fi
done
