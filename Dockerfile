# https://frankenphp.dev/docs/docker/#how-to-install-more-php-extensions

FROM dunglas/frankenphp

# add additional extensions here:
RUN install-php-extensions \
    pdo_mysql \
    gd \
    intl \
    zip \
    opcache

# Needed for testing creating release
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apt-get update \
    && apt-get --yes install git rsync
