FROM nginx:stable-alpine

LABEL maintainer="Davide Porrovecchio <davide.porrovecchio@agid.gov.it>"

ADD nginx.conf /etc/nginx/

RUN adduser -D -H -u 1000 -s /bin/sh -G www-data -g www-data www-data

ADD ./certs/server.crt /var/ssl/
ADD ./certs/server.key /var/ssl/

ARG PHP_UPSTREAM_CONTAINER=php-fpm
ARG PHP_UPSTREAM_PORT=9000
ARG ALLOWED_ANCESTORS=https://localhost

# Remove the default conf
RUN rm /etc/nginx/conf.d/default.conf

ADD ./conf/ssl.conf /etc/nginx/snippets/ssl.conf
ADD ./conf/upstream.conf /etc/nginx/conf.d/
ADD ./conf/application.conf /etc/nginx/sites-available/
ADD ./conf/matomo.conf /etc/nginx/sites-available/

RUN sed -i -e s/@PHP_UPSTREAM_CONTAINER@/${PHP_UPSTREAM_CONTAINER}/g /etc/nginx/conf.d/upstream.conf
RUN sed -i -e s/@PHP_UPSTREAM_PORT@/${PHP_UPSTREAM_PORT}/g /etc/nginx/conf.d/upstream.conf
RUN sed -i -e s#@ALLOWED_ANCESTORS@#${ALLOWED_ANCESTORS}#g /etc/nginx/sites-available/matomo.conf

CMD ["nginx"]

EXPOSE 80 443 9000 9443
