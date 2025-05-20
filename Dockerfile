ARG PHP_VERSION

###############################

FROM composer:2.8.9 AS composer

###############################

FROM php:${PHP_VERSION}-cli-bookworm

RUN apt-get update \
    && apt-get install --yes \
        $PHPIZE_DEPS \
        ghostscript \
        libmagickcore-dev \
        libmagickwand-dev \
        unzip \
        wkhtmltopdf \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && rm -rf /tmp/pear \
    && php -m | grep imagick

RUN if [ -f /etc/ImageMagick-*/policy.xml ] ; \
        then sed -i 's/<policy domain="coder" rights="none" pattern="PDF" \/>/<policy domain="coder" rights="read|write" pattern="PDF" \/>/g' /etc/ImageMagick-*/policy.xml ; \
        else echo did not see file /etc/ImageMagick-*/policy.xml ; \
    fi

RUN echo "zend.assertions=1" >> ${PHP_INI_DIR}/conf.d/php.ini 
RUN echo "memory_limit=-1" >> ${PHP_INI_DIR}/conf.d/php.ini 

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY                 ./                /app

WORKDIR /app
