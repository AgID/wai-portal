FROM nginx:stable-alpine

LABEL maintainer="Davide Porrovecchio <davide.porrovecchio@agid.gov.it>"

ADD nginx.conf /etc/nginx/

RUN adduser -D -H -u 1000 -s /bin/bash www-data

# RUN mkdir /var/letsencrypt && certbot certonly --webroot -w /var/letsencrypt --staging -n --agree-tos --email mail@example.com -d example.com

ADD ./certs/server.crt /var/ssl/
ADD ./certs/server.key /var/ssl/

ARG PHP_UPSTREAM_CONTAINER=php-fpm
ARG PHP_UPSTREAM_PORT=9000

# Remove the default conf
RUN rm /etc/nginx/conf.d/default.conf

ADD ./conf/upstream.conf /etc/nginx/conf.d/
ADD ./conf/application.conf /etc/nginx/sites-available/
ADD ./conf/matomo.conf /etc/nginx/sites-available/


CMD ["nginx"]

EXPOSE 80 443 9000 9443
