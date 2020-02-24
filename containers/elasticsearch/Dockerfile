ARG ELASTICSEARCH_VERSION

FROM docker.elastic.co/elasticsearch/elasticsearch:${ELASTICSEARCH_VERSION}

COPY ./check_index.sh /opt/check_index.sh

COPY ./index_settings.json /opt/elasticsearch-init/index_settings.json

COPY ./log_search_template.json /opt/elasticsearch-init/log_search_template.json

COPY ./initialize_index.sh /opt/elasticsearch-init/initialize_index.sh

COPY ./initialize_search_template.sh /opt/elasticsearch-init/initialize_search_template.sh

ARG ELASTICSEARCH_INDEX_TEMPLATE_NAME
RUN sed -i -e s/@ELASTICSEARCH_INDEX_TEMPLATE_NAME@/${ELASTICSEARCH_INDEX_TEMPLATE_NAME}/g /opt/elasticsearch-init/initialize_index.sh
RUN sed -i -e s/@ELASTICSEARCH_INDEX_TEMPLATE_NAME@/${ELASTICSEARCH_INDEX_TEMPLATE_NAME}/g /opt/elasticsearch-init/index_settings.json
RUN sed -i -e s/@ELASTICSEARCH_INDEX_TEMPLATE_NAME@/${ELASTICSEARCH_INDEX_TEMPLATE_NAME}/g /opt/check_index.sh

ARG ELASTICSEARCH_SEARCH_TEMPLATE_NAME
RUN sed -i -e s/@ELASTICSEARCH_SEARCH_TEMPLATE_NAME@/${ELASTICSEARCH_SEARCH_TEMPLATE_NAME}/g /opt/elasticsearch-init/initialize_search_template.sh
