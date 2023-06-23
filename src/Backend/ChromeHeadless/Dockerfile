FROM composer:2.5.8 as composer

###############

FROM php:8.0.29-alpine3.16

RUN mkdir -p /opt/app \
    && adduser -D docker \
    && chown -R docker:docker /opt/app

USER docker

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /opt/app

COPY --chown=docker . /opt/app

RUN composer install

ENTRYPOINT ["/bin/sh", "-c"]
CMD ["vendor/bin/phpspec run"]
