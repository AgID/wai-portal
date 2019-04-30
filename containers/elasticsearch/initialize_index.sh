#!/bin/sh

# Create the index
RESPONSE=$(curl -w %{http_code} -so /dev/null -X PUT -so /dev/null -H 'Content-Type: application/json' http://elasticsearch:9200/@ELASTICSEARCH_INDEX_NAME@ -d @/opt/elasticsearch-init/index_settings.json)
if [[ ${RESPONSE} -ne 200 ]]
then
    exit 1
fi
