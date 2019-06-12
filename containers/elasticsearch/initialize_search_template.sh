#!/bin/sh

# Recreate the search template
RESPONSE=$(curl -w %{http_code} -so /dev/null -X POST -H 'Content-Type: application/json' http://elasticsearch:9200/_scripts/@ELASTICSEARCH_SEARCH_TEMPLATE_NAME@ -d @/opt/elasticsearch-init/log_search_template.json)
if [[ ${RESPONSE} -ne 200 ]]
then
    exit 1
fi
