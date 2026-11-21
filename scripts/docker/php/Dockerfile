ARG PHP_VERSION=7.4
FROM php:${PHP_VERSION}-cli

COPY scripts/docker/php/install-tools.sh /usr/local/bin/bbcode-parser-install-tools.sh
RUN sh /usr/local/bin/bbcode-parser-install-tools.sh

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
