#!/bin/sh

RESPONSE=$(curl -w %{http_code} -so /dev/null -m 60 -X GET -H 'Content-Type: application/json' http://elasticsearch:9200/_cluster/health?wait_for_status=yellow&timeout=60s)
while [[ ${RESPONSE} -ne 200 ]]
do
    RESPONSE=$(curl -w %{http_code} -so /dev/null -m 60 -X GET -H 'Content-Type: application/json' http://elasticsearch:9200/_cluster/health?wait_for_status=yellow&timeout=60s)
done

# Check if index already exists
RESPONSE=$(curl -w %{http_code} -so /dev/null -X GET -H 'Content-Type: application/json' http://elasticsearch:9200/_template/@ELASTICSEARCH_INDEX_TEMPLATE_NAME@)
if [[ ${RESPONSE} -ne 200 ]]
then
    exit 1
fi
