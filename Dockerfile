# https://frankenphp.dev/docs/docker/#how-to-install-more-php-extensions

FROM dunglas/frankenphp

# add additional extensions here:
RUN install-php-extensions \
    pdo_mysql \
    gd \
    intl \
    zip \
    opcache
