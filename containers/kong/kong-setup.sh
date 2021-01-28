#!/bin/sh
# kong-setup.sh

set -e

KONG_ENDPOINT_FRONTEND_URL="$1"

INSTALL_PLUGIN=`curl -X POST -s ${KONG_ENDPOINT_FRONTEND_URL}/plugins/ --data "name=prometheus" > /dev/null`
INSTALL_PLUGIN=`curl -X POST -s ${KONG_ENDPOINT_FRONTEND_URL}/plugins/ --data "name=oauth2&config.enable_client_credentials=true&config.global_credentials=true&config.accept_http_if_already_terminated=true" > /dev/null`

ADD_SERVICE=`curl -X POST -s ${KONG_ENDPOINT_FRONTEND_URL}/services --data "name=portal&url=https://nginx/api" > /dev/null`
ADD_ROUTE=`curl -X POST -s ${KONG_ENDPOINT_FRONTEND_URL}/services/portal/routes  --data "paths[]=/portal&name=portal" > /dev/null`

>&2 echo "Kong plugins and routes are ready ..."