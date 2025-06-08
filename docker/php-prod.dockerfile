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

COPY . /var/www/
RUN mv .env.prod .env \
    && composer install --no-scripts --no-progress --no-interaction \
    && chown -R ${UID}:${GID} /var/www \
    && chmod 777 -R /var/www \
    && rm .env*


USER ${USER}



FROM base AS php
COPY ./docker/php-fpm.conf /usr/local/etc
COPY ./docker/supervisord-php.conf /etc/supervisor/conf.d/supervisord-php.conf
CMD /var/www/docker/migrate-build-and-run-supervisor.sh "${USER}" /etc/supervisor/conf.d/supervisord-php.conf migrate-and-build


FROM base AS cron
CMD ["php", "/var/www/artisan", "schedule:work"]

FROM base AS queue
COPY ./docker/supervisord-queue.conf /etc/supervisor/conf.d/supervisord-queue.conf
CMD /var/www/docker/migrate-build-and-run-supervisor.sh "${USER}" /etc/supervisor/conf.d/supervisord-queue.conf

