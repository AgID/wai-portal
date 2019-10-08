#!/bin/sh

exec docker-entrypoint.sh redis-sentinel /usr/local/etc/redis/sentinel.conf
