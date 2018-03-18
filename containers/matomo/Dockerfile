FROM alpine:latest

LABEL maintainer="Davide Porrovecchio <davide.porrovecchio@agid.gov.it>"

RUN apk --update --no-cache add \
    curl gnupg

ENV PIWIK_VERSION 3.3.0

RUN curl -fsSL -o piwik.tar.gz \
      "https://builds.piwik.org/piwik-${PIWIK_VERSION}.tar.gz" \
 && curl -fsSL -o piwik.tar.gz.asc \
      "https://builds.piwik.org/piwik-${PIWIK_VERSION}.tar.gz.asc" \
 && export GNUPGHOME="$(mktemp -d)" \
 && gpg --keyserver ipv4.pool.sks-keyservers.net --recv-keys 814E346FA01A20DBB04B6807B5DBD5925590A237 \
 && gpg --batch --verify piwik.tar.gz.asc piwik.tar.gz \
 && mkdir /opt \
 && tar -xzf piwik.tar.gz -C /opt \
 && rm piwik.tar.gz

RUN curl -fsSL -o /opt/piwik/misc/GeoIPCity.dat.gz http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz \
 && gunzip /opt/piwik/misc/GeoIPCity.dat.gz

COPY ./matomo.sql /opt/matomo.sql
COPY ./install_matomo_db.sh /opt/install_matomo_db.sh
COPY ./config.ini.php /opt/piwik/config/config.ini.php

VOLUME /opt/
