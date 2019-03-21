FROM busybox:latest

LABEL maintainer="Davide Porrovecchio <davide.porrovecchio@agid.gov.it>"

RUN mkdir -p /opt/matomo

ENV MATOMO_VERSION 3.9.0

RUN wget -q -O matomo.tar.gz \
        "https://builds.matomo.org/matomo-${MATOMO_VERSION}.tar.gz"; \
    tar -xzf matomo.tar.gz -C /opt/matomo --strip-components=1; \
    rm matomo.tar.gz;

RUN wget -q -O GeoIPCity.tar.gz \
        "https://geolite.maxmind.com/download/geoip/database/GeoLite2-City.tar.gz"; \
    tar -xf GeoIPCity.tar.gz -C /opt/matomo/misc/ --strip-components=1 --wildcards "*.mmdb"; \
    rm -rf GeoIPCity.tar.gz

COPY ./matomo.sql /opt/matomo.sql
COPY ./install_matomo_db.sh /opt/install_matomo_db.sh
COPY ./config.ini.php /opt/matomo/config/config.ini.php

ARG DB_PASSWORD
RUN sed -i -e s/@DB_PASSWORD@/${DB_PASSWORD}/g /opt/install_matomo_db.sh
RUN sed -i -e s/@DB_PASSWORD@/${DB_PASSWORD}/g /opt/matomo/config/config.ini.php

ARG MATOMO_ROOT_USER
RUN sed -i -e s/@MATOMO_ROOT_USER@/${MATOMO_ROOT_USER}/g /opt/install_matomo_db.sh

ARG MATOMO_ROOT_PASSWORD
RUN sed -i -e s#@MATOMO_ROOT_PASSWORD@#${MATOMO_ROOT_PASSWORD}#g /opt/install_matomo_db.sh

ARG MATOMO_ROOT_APIKEY
RUN sed -i -e s/@MATOMO_ROOT_APIKEY@/${MATOMO_ROOT_APIKEY}/g /opt/install_matomo_db.sh

VOLUME /opt
