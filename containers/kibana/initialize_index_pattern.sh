#!/bin/sh

# Check index-pattern existence
RESPONSE=$( curl -w %{http_code} -so /dev/null -X GET -H 'kbn-xsrf: true' -H 'Content-Type: application/json' http://kibana:5601/api/saved_objects/index-pattern/46a83650-7732-11e9-b2a1-2f0d1696b129)
if [[ ${RESPONSE} -ne 200 ]]
then
    # Create the index-pattern
    RESPONSE=$( curl -w %{http_code} -so /dev/null -X POST -H 'kbn-xsrf: true' -H 'Content-Type: application/json' http://kibana:5601/api/saved_objects/index-pattern/46a83650-7732-11e9-b2a1-2f0d1696b129 -d @/opt/kibana-init/index_pattern.json)
    if [[ ${RESPONSE} -ne 200 ]]
    then
        exit 1
    fi
fi
