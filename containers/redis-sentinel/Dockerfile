FROM redis:latest

COPY ./sentinel.conf /usr/local/etc/redis/sentinel.conf
ARG REDIS_SENTINEL_SERVICE
RUN sed -i -e s/@REDIS_SENTINEL_SERVICE@/${REDIS_SENTINEL_SERVICE}/g /usr/local/etc/redis/sentinel.conf
COPY ./entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
