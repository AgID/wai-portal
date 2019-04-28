ARG PHP_VERSION

FROM php:${PHP_VERSION}-alpine

LABEL maintainer="Davide Porrovecchio <davide.porrovecchio@agid.gov.it>"

# supervisord
RUN apk --update add supervisor

# php required extensions
RUN docker-php-ext-install pdo_mysql bcmath

# clean apk cache
RUN rm /var/cache/apk/*

# supervisord conf
COPY supervisord.conf /etc/supervisord.conf

# cron scheduler
COPY ./cron-laravel-scheduler /etc/cron.d/
RUN chmod 644 /etc/cron.d/cron-laravel-scheduler

# entrypoint
COPY ./entrypoint.sh /entrypoint.sh
RUN chmod 744 /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

WORKDIR /etc/supervisor/conf.d/
