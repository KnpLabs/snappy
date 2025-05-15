FROM composer:2.6.3 AS composer

###############################

FROM php:8.2.10-fpm-alpine3.18

WORKDIR /src

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY ./entrypoint /usr/local/share/entrypoint
COPY ./src /src

ENTRYPOINT ["/usr/local/share/entrypoint"]
