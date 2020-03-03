#!/bin/sh
crontab /cron-laravel-scheduler
# wait 30 seconds for the build process to be completed
sleep 30 && crond -l 8
/usr/bin/supervisord -n -c /etc/supervisord.conf
