#!/bin/sh
crond
/usr/bin/supervisord -n -c /etc/supervisord.conf
