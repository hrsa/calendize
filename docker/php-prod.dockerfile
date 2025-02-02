FROM serversideup/php:8.4-fpm AS base

ARG UID
ARG GID
ARG USER

ENV UID=${UID}
ENV GID=${GID}
ENV USER=${USER}
ENV PHP_OPCACHE_ENABLE=1
ENV APP_BASE_DIR='/var/www'

WORKDIR /var/www/

USER root

RUN addgroup --gid ${GID} --system ${USER}
RUN adduser --system --home /home/${USER} --shell /bin/sh --uid ${UID} --ingroup ${USER} ${USER}

RUN apt-get update && apt-get install -y \
    jpegoptim optipng pngquant gifsicle \
    curl \
    supervisor \
    nano \
    locales

RUN install-php-extensions intl exif bcmath bz2 gd


RUN curl -fsSL https://deb.nodesource.com/setup_23.x | bash -
RUN apt-get install -y nodejs
RUN npm install -g npm@11.1.0 && npm install -g npm-check-updates

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/
RUN mv .env.prod .env

RUN composer install --no-scripts --no-progress

RUN chown -R ${UID}:${GID} /var/www
RUN chmod 777 -R /var/www
RUN rm .env*

USER ${USER}



FROM base AS php
COPY ./docker/php-fpm.conf /usr/local/etc
COPY ./docker/supervisord-php.conf /etc/supervisor/conf.d/supervisord-php.conf
CMD /var/www/docker/migrate-build-and-run-supervisor.sh ${USER} /etc/supervisor/conf.d/supervisord-php.conf migrate-and-build


FROM base AS cron
CMD ["php", "/var/www/artisan", "schedule:work"]

FROM base AS queue
COPY ./docker/supervisord-queue.conf /etc/supervisor/conf.d/supervisord-queue.conf
CMD /var/www/docker/migrate-build-and-run-supervisor.sh ${USER} /etc/supervisor/conf.d/supervisord-queue.conf
