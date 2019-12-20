ARG PHP_VERSION

FROM php:${PHP_VERSION}-alpine

LABEL maintainer="Davide Porrovecchio <davide.porrovecchio@agid.gov.it>"

# supervisord
RUN apk --update add supervisor icu-dev

# php required extensions
RUN docker-php-ext-install pdo_mysql bcmath intl

# clean apk
RUN rm /var/cache/apk/*

ARG APP_CODE_PATH_CONTAINER

# supervisord conf
COPY supervisord.conf /etc/supervisord.conf
RUN sed -i -e s#@APP_CODE_PATH_CONTAINER@#${APP_CODE_PATH_CONTAINER}#g /etc/supervisord.conf

# cron scheduler
COPY ./cron-laravel-scheduler /
RUN sed -i -e s#@APP_CODE_PATH_CONTAINER@#${APP_CODE_PATH_CONTAINER}#g /cron-laravel-scheduler

# entrypoint
COPY ./entrypoint.sh /entrypoint.sh
RUN chmod 744 /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

WORKDIR /etc/supervisor/conf.d/
