#
#--------------------------------------------------------------------------
# Image Setup
#--------------------------------------------------------------------------
#
# This 'php-fpm' is a simplified version of the Laradock 'php-fpm'
# Maintained by Mahmoud Zalt <mahmoud@zalt.me>
# See: https://github.com/Laradock/php-fpm
#

FROM laradock/php-fpm:2.2-71

LABEL maintainer="Davide Porrovecchio <davide.porrovecchio@agid.gov.it>"

#
#--------------------------------------------------------------------------
# Mandatory Software's Installation
#--------------------------------------------------------------------------
#
# Mandatory Software's such as ("mcrypt", "pdo_mysql", "libssl-dev", ....)
# are installed on the base image 'laradock/php-fpm' image. If you want
# to add more Software's or remove existing one, you need to edit the
# base image (https://github.com/Laradock/php-fpm).
#

#
#--------------------------------------------------------------------------
# Optional Software's Installation
#--------------------------------------------------------------------------
#
# Optional Software's will only be installed if you set them to `true`
# in the `docker-compose.yml` before the build.
# Example:
#   - INSTALL_ZIP_ARCHIVE=true
#

#####################################
# xDebug:
#####################################

ARG INSTALL_XDEBUG=false
RUN if [ ${INSTALL_XDEBUG} = true ]; then \
    # Install the xdebug extension
    pecl channel-update pecl.php.net && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug \
;fi

# Copy xdebug configuration for remote debugging
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

#####################################
# PHP REDIS EXTENSION FOR PHP 7.0
#####################################

ARG INSTALL_PHPREDIS=false
RUN if [ ${INSTALL_PHPREDIS} = true ]; then \
    # Install Php Redis Extension
    pecl channel-update pecl.php.net && \
    printf "\n" | pecl install -o -f redis && \
    rm -rf /tmp/pear && \
    docker-php-ext-enable redis \
;fi

#####################################
# Opcache:
#####################################

ARG INSTALL_OPCACHE=false
RUN if [ ${INSTALL_OPCACHE} = true ]; then \
    docker-php-ext-install opcache \
;fi

# Copy opcache configration
COPY ./opcache.ini /usr/local/etc/php/conf.d/opcache.ini

#
#--------------------------------------------------------------------------
# Final Touch
#--------------------------------------------------------------------------
#

COPY ./laravel.ini /usr/local/etc/php/conf.d
COPY ./xlaravel.pool.conf /usr/local/etc/php-fpm.d/
COPY ./php.ini /usr/local/etc/php/php.ini

#RUN rm -r /var/lib/apt/lists/*

RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data

WORKDIR /var/www

CMD ["php-fpm"]

EXPOSE 9000
