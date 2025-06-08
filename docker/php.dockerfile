FROM serversideup/php:8.4-fpm AS base

ARG UID
ARG GID
ARG USER

ENV UID=${UID} \
    GID=${GID} \
    USER=${USER} \
    PHP_OPCACHE_ENABLE=1 \
    APP_BASE_DIR='/var/www'

WORKDIR ${APP_BASE_DIR}

USER root

RUN addgroup --gid ${GID} --system ${USER} \
    && adduser --system --home /home/${USER} --shell /bin/sh --uid ${UID} --ingroup ${USER} ${USER}

RUN apt-get update && apt-get install -y --no-install-recommends \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    curl \
    supervisor \
    nano \
    locales \
    && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
    intl \
    exif \
    bcmath \
    bz2 \
    gd

RUN curl -fsSL https://deb.nodesource.com/setup_24.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN chown -R ${UID}:${GID} /var/www \
    && chmod 777 -R /var/www

USER ${USER}


FROM base AS php
COPY ./docker/php-fpm.conf /usr/local/etc
COPY ./docker/supervisord-php.conf /etc/supervisor/conf.d/supervisord-php.conf
CMD /usr/bin/supervisord -u ${USER} -n -c /etc/supervisor/conf.d/supervisord-php.conf

FROM base AS cron
CMD ["php", "/var/www/artisan", "schedule:work"]

FROM base AS queue
COPY ./docker/supervisord-queue.conf /etc/supervisor/conf.d/supervisord-queue.conf
CMD /usr/bin/supervisord -u ${USER} -n -c /etc/supervisor/conf.d/supervisord-queue.conf

FROM base AS composer
ENTRYPOINT ["composer"]

FROM base AS npm
ENTRYPOINT [ "npm" ]

FROM base AS ncu
ENTRYPOINT [ "ncu", "--interactive", "--format", "group" ]
