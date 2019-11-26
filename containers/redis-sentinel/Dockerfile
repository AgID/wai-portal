FROM redis:latest

COPY ./sentinel.conf /usr/local/etc/redis/sentinel.conf
ARG REDIS_SENTINEL_SET
ARG REDIS_IPA_INDEX_SENTINEL_SET
RUN sed -i -e s/@REDIS_SENTINEL_SET@/${REDIS_SENTINEL_SET}/g /usr/local/etc/redis/sentinel.conf
RUN sed -i -e s/@REDIS_IPA_INDEX_SENTINEL_SET@/${REDIS_IPA_INDEX_SENTINEL_SET}/g /usr/local/etc/redis/sentinel.conf
COPY ./entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
