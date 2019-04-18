FROM busybox:latest

LABEL maintainer="Davide Porrovecchio <davide.porrovecchio@agid.gov.it>"

RUN mkdir -p /opt/matomo

ENV MATOMO_VERSION 3.9.0
ENV DISABLE_TRACKING_VERSION 1.0.4

RUN wget -q -O matomo.tar.gz \
        "https://builds.matomo.org/matomo-${MATOMO_VERSION}.tar.gz"; \
    tar -xzf matomo.tar.gz -C /opt/matomo --strip-components=1; \
    rm matomo.tar.gz;

RUN wget -q -O GeoIPCity.tar.gz \
        "https://geolite.maxmind.com/download/geoip/database/GeoLite2-City.tar.gz"; \
    mkdir /tmp/geocity; \
    tar -xf GeoIPCity.tar.gz -C /tmp/geocity/ --strip-components=1; \
    mv /tmp/geocity/*.mmdb /opt/matomo/misc/; \
    rm -rf GeoIPCity.tar.gz /tmp/geocity

RUN wget -q -O DisableTracking.tar.gz \
        "https://github.com/Valair/Piwik-Plugin-DisableTracking/archive/${DISABLE_TRACKING_VERSION}.tar.gz"; \
    tar -xf DisableTracking.tar.gz -C /opt/matomo/plugins; \
    mv /opt/matomo/plugins/Piwik-Plugin-DisableTracking-${DISABLE_TRACKING_VERSION} /opt/matomo/plugins/DisableTracking; \
    rm -rf DisableTracking.tar.gz

COPY ./matomo.sql /opt/matomo.sql
COPY ./install_matomo_db.sh /opt/install_matomo_db.sh

COPY ./disable_tracking_plugin.sql /opt/disable_tracking_plugin.sql
COPY ./install_disable_tracking_plugin.sh /opt/install_disable_tracking_plugin.sh

COPY ./config.ini.php /opt/matomo/config/config.ini.php

ARG DB_PASSWORD
RUN sed -i -e s/@DB_PASSWORD@/${DB_PASSWORD}/g /opt/install_matomo_db.sh
RUN sed -i -e s/@DB_PASSWORD@/${DB_PASSWORD}/g /opt/matomo/config/config.ini.php
RUN sed -i -e s/@DB_PASSWORD@/${DB_PASSWORD}/g /opt/install_disable_tracking_plugin.sh

ARG MATOMO_ROOT_USER
RUN sed -i -e s/@MATOMO_ROOT_USER@/${MATOMO_ROOT_USER}/g /opt/install_matomo_db.sh

ARG MATOMO_ROOT_PASSWORD
RUN sed -i -e s#@MATOMO_ROOT_PASSWORD@#${MATOMO_ROOT_PASSWORD}#g /opt/install_matomo_db.sh

ARG MATOMO_ROOT_APIKEY
RUN sed -i -e s/@MATOMO_ROOT_APIKEY@/${MATOMO_ROOT_APIKEY}/g /opt/install_matomo_db.sh

VOLUME /opt
