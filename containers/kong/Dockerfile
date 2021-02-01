ARG KONG_VERSION

FROM kong:${KONG_VERSION}

LABEL maintainer="Davide Porrovecchio <davide.porrovecchio@agid.gov.it>"

USER root

COPY ./kong-setup.sh /kong-setup.sh
COPY ./kong-start.sh /kong-start.sh

RUN apk --update add curl && rm /var/cache/apk/* \
    && chmod 775 /kong-start.sh \
    && chmod 775 /kong-setup.sh

USER kong

ENTRYPOINT [ "/kong-start.sh" ]
