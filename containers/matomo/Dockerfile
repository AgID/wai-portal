FROM alpine:latest

LABEL maintainer="Davide Porrovecchio <davide.porrovecchio@agid.gov.it>"

RUN apk --update add curl && rm /var/cache/apk/*

RUN mkdir -p /opt/matomo
ARG MATOMO_VERSION
ARG MATOMO_WAI_THEME_VERSION
ARG MATOMO_PLUGIN_DISABLESITETRACKING_VERSION
ARG MATOMO_PLUGIN_QUEUEDTRACKING_VERSION
ARG MATOMO_PLUGIN_PROTECTTRACKID_VERSION
ARG MATOMO_PLUGIN_CUSTOMDIMENSIONS_VERSION
ARG MATOMO_PLUGIN_LOGINFILTERIP_VERSION

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

RUN curl -fsSL -o WAIMatomoTheme.tar.gz \
        "https://github.com/agid/wai-matomo-theme/releases/download/v${MATOMO_WAI_THEME_VERSION}/wai-matomo-theme_${MATOMO_WAI_THEME_VERSION}_auto_activate.tar.gz" \
    && tar -xf WAIMatomoTheme.tar.gz -C /opt/matomo/plugins \
    && rm -rf WAIMatomoTheme.tar.gz

RUN if [ -n "$MATOMO_PLUGIN_DISABLESITETRACKING_VERSION" ]; then \
    curl -fsSL -o DisableTracking.tar.gz \
        "https://github.com/AgID/wai-matomo-plugin-DisableTracking/releases/download/${MATOMO_PLUGIN_DISABLESITETRACKING_VERSION}/DisableTracking.tar.gz" \
    && tar -xf DisableTracking.tar.gz -C /opt/matomo/plugins \
    && rm -rf DisableTracking.tar.gz \
;fi

RUN if [ -n "$MATOMO_PLUGIN_QUEUEDTRACKING_VERSION" ]; then \
    curl -fsSL -o QueuedTracking.zip \
        "https://plugins.matomo.org/api/2.0/plugins/QueuedTracking/download/${MATOMO_PLUGIN_QUEUEDTRACKING_VERSION}" \
    && unzip QueuedTracking.zip -d /opt/matomo/plugins \
    && rm -rf QueuedTracking.zip \
;fi

RUN if [ -n "$MATOMO_PLUGIN_PROTECTTRACKID_VERSION" ]; then \
    curl -fsSL -o ProtectTrackID.zip \
        "https://plugins.matomo.org/api/2.0/plugins/ProtectTrackID/download/${MATOMO_PLUGIN_PROTECTTRACKID_VERSION}" \
    && unzip ProtectTrackID.zip -d /opt/matomo/plugins \
    && rm -rf ProtectTrackID.zip \
;fi

RUN if [ -n "$MATOMO_PLUGIN_CUSTOMDIMENSIONS_VERSION" ]; then \
    curl -fsSL -o CustomDimensions.zip \
        "https://plugins.matomo.org/api/2.0/plugins/CustomDimensions/download/${MATOMO_PLUGIN_CUSTOMDIMENSIONS_VERSION}" \
    && unzip CustomDimensions.zip -d /opt/matomo/plugins \
    && rm -rf CustomDimensions.zip \
;fi

RUN if [ -n "$MATOMO_PLUGIN_LOGINFILTERIP_VERSION" ]; then \
    curl -fsSL -o LoginFilterIp.zip \
        "https://github.com/AgID/wai-matomo-plugin-LoginFilterIp/archive/${MATOMO_PLUGIN_LOGINFILTERIP_VERSION}.zip" \
    && unzip LoginFilterIp.zip -d /opt/matomo/plugins \
    && rm -rf LoginFilterIp.zip \
;fi

COPY ./matomo.sql /opt/matomo-install/matomo.sql
COPY ./install_matomo_db.sh /opt/matomo-install/install_matomo_db.sh

COPY ./disable_tracking_plugin.sql /opt/matomo-install/disable_tracking_plugin.sql
COPY ./install_disable_tracking_plugin.sh /opt/matomo-install/install_disable_tracking_plugin.sh

COPY ./config.ini.php /opt/matomo/config/config.ini.php

ARG MATOMO_WAI_URL
RUN sed -i -e s/@MATOMO_WAI_URL@/${MATOMO_WAI_URL}/g /opt/matomo/config/config.ini.php

ARG DB_PASSWORD
RUN sed -i -e s/@DB_PASSWORD@/${DB_PASSWORD}/g /opt/matomo-install/install_matomo_db.sh
RUN sed -i -e s/@DB_PASSWORD@/${DB_PASSWORD}/g /opt/matomo-install/install_disable_tracking_plugin.sh

ARG MATOMO_DB_READER
ARG MATOMO_DB_USER
ARG MATOMO_DB_PASSWORD
RUN sed -i -e s/@MATOMO_DB_READER@/${MATOMO_DB_READER}/g /opt/matomo/config/config.ini.php
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
