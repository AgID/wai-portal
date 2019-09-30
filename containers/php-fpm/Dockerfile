ARG PHP_VERSION

FROM php:${PHP_VERSION}-fpm

LABEL maintainer="Davide Porrovecchio <davide.porrovecchio@agid.gov.it>"

ENV DEBIAN_FRONTEND noninteractive

# php required extension
RUN docker-php-ext-install pdo_mysql bcmath

# xdebug
ARG INSTALL_XDEBUG=false
RUN if [ ${INSTALL_XDEBUG} = true ]; then \
    pecl channel-update pecl.php.net && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug \
;fi

# xdebug conf
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# dusk deps
ARG ENV=local
ARG CHROME_BROWSER_CHANNEL=stable
RUN if [ "env-${ENV}" = "env-local" ] || [ "env-${ENV}" = "env-testing" ]; then \
    apt-get update \
    && apt-get -y --no-install-recommends install apt-utils wget unzip libnss3 libgconf-2-4 xdg-utils hicolor-icon-theme \
    && chrome_version=`wget -qO- http://omahaproxy.appspot.com/history | awk -v CHANNEL="$CHROME_BROWSER_CHANNEL" -F [,\.] '$1 == "linux" && $2 == CHANNEL {print $3"."$4"."$5}' | head -1` \
    && wget https://dl.google.com/linux/direct/google-chrome-${CHROME_BROWSER_CHANNEL}_current_amd64.deb \
    && dpkg -i --force-depends google-chrome-${CHROME_BROWSER_CHANNEL}_current_amd64.deb \
    && apt-get -y -f --no-install-recommends install \
    && dpkg -i --force-depends google-chrome-${CHROME_BROWSER_CHANNEL}_current_amd64.deb \
    && rm google-chrome-${CHROME_BROWSER_CHANNEL}_current_amd64.deb \
    && chrome_driver_version=`wget -qO- http://chromedriver.storage.googleapis.com/LATEST_RELEASE_"$chrome_version"` \
    && wget https://chromedriver.storage.googleapis.com/"$chrome_driver_version"/chromedriver_linux64.zip \
    && unzip -q chromedriver_linux64.zip \
    && mv chromedriver /usr/local/bin/ \
    && rm chromedriver_linux64.zip \
    && wget -O node-build.zip https://github.com/nodenv/node-build/archive/master.zip \
    && unzip node-build.zip \
    && node-build-master/install.sh \
    && rm -Rf node-build-master node-build.zip \
;fi

# configuration files
COPY ./laravel.ini /usr/local/etc/php/conf.d
COPY ./xlaravel.pool.conf /usr/local/etc/php-fpm.d/
COPY ./php.ini /usr/local/etc/php/php.ini

# set uid and gid
RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data

# cleanup
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /var/www

CMD ["php-fpm"]

EXPOSE 9000
