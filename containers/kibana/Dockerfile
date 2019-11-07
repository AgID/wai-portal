FROM docker.elastic.co/kibana/kibana:7.4.1

COPY ./check_index_pattern.sh /opt/check_index_pattern.sh

COPY ./index_pattern.json /opt/kibana-init/index_pattern.json

COPY ./initialize_index_pattern.sh /opt/kibana-init/initialize_index_pattern.sh

USER root
RUN chown -R kibana:root /opt/kibana-init/

USER kibana
ARG ELASTICSEARCH_INDEX_PREFIX
RUN sed -i -e s/@ELASTICSEARCH_INDEX_PREFIX@/${ELASTICSEARCH_INDEX_PREFIX}/g /opt/kibana-init/index_pattern.json
