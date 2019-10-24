FROM alpine:latest

LABEL maintainer="Davide Porrovecchio <davide.porrovecchio@agid.gov.it>"

RUN apk --update add curl && rm /var/cache/apk/*

RUN mkdir -p /opt/matomo

ARG MATOMO_VERSION
ARG DISABLE_TRACKING_VERSION=1.0.4

RUN curl -fsSL -o matomo.tar.gz \
        "https://builds.matomo.org/matomo-${MATOMO_VERSION}.tar.gz"; \
    tar -xzf matomo.tar.gz -C /opt/matomo --strip-components=1; \
    rm matomo.tar.gz;

RUN curl -fsSL -o GeoIPCity.tar.gz \
        "https://geolite.maxmind.com/download/geoip/database/GeoLite2-City.tar.gz" \
    && mkdir /tmp/geocity \
    && tar -xf GeoIPCity.tar.gz -C /tmp/geocity/ --strip-components=1 \
    && mv /tmp/geocity/*.mmdb /opt/matomo/misc/ \
    && rm -rf GeoIPCity.tar.gz /tmp/geocity

RUN curl -fsSL -o DisableTracking.tar.gz \
        "https://github.com/Valair/Piwik-Plugin-DisableTracking/archive/${DISABLE_TRACKING_VERSION}.tar.gz" \
    && tar -xf DisableTracking.tar.gz -C /opt/matomo/plugins \
    && mv /opt/matomo/plugins/Piwik-Plugin-DisableTracking-${DISABLE_TRACKING_VERSION} /opt/matomo/plugins/DisableTracking \
    && rm -rf DisableTracking.tar.gz

COPY ./matomo.sql /opt/matomo-install/matomo.sql
COPY ./install_matomo_db.sh /opt/matomo-install/install_matomo_db.sh

COPY ./disable_tracking_plugin.sql /opt/matomo-install/disable_tracking_plugin.sql
COPY ./install_disable_tracking_plugin.sh /opt/matomo-install/install_disable_tracking_plugin.sh

COPY ./config.ini.php /opt/matomo/config/config.ini.php

ARG DB_PASSWORD
RUN sed -i -e s/@DB_PASSWORD@/${DB_PASSWORD}/g /opt/matomo-install/install_matomo_db.sh
RUN sed -i -e s/@DB_PASSWORD@/${DB_PASSWORD}/g /opt/matomo-install/install_disable_tracking_plugin.sh

ARG MATOMO_DB_USER
ARG MATOMO_DB_PASSWORD
RUN sed -i -e s/@MATOMO_DB_USER@/${MATOMO_DB_USER}/g /opt/matomo/config/config.ini.php
RUN sed -i -e s/@MATOMO_DB_PASSWORD@/${MATOMO_DB_PASSWORD}/g /opt/matomo/config/config.ini.php

ARG MATOMO_ROOT_USER
RUN sed -i -e s/@MATOMO_ROOT_USER@/${MATOMO_ROOT_USER}/g /opt/matomo-install/install_matomo_db.sh
RUN sed -i -e s/@MATOMO_DB_USER@/${MATOMO_DB_USER}/g /opt/matomo-install/install_matomo_db.sh
RUN sed -i -e s/@MATOMO_DB_PASSWORD@/${MATOMO_DB_PASSWORD}/g /opt/matomo-install/install_matomo_db.sh

ARG MATOMO_ROOT_PASSWORD
RUN sed -i -e s#@MATOMO_ROOT_PASSWORD@#${MATOMO_ROOT_PASSWORD}#g /opt/matomo-install/install_matomo_db.sh

ARG MATOMO_ROOT_APIKEY
RUN sed -i -e s/@MATOMO_ROOT_APIKEY@/${MATOMO_ROOT_APIKEY}/g /opt/matomo-install/install_matomo_db.sh
