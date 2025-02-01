FROM serversideup/php:8.4-fpm AS base

ARG UID
ARG GID
ARG USER

ENV UID=${UID}
ENV GID=${GID}
ENV USER=${USER}

WORKDIR /var/www/

USER root

RUN addgroup --gid ${GID} --system ${USER}
RUN adduser --system --home /home/${USER} --shell /bin/sh --uid ${UID} --ingroup ${USER} ${USER}

RUN apt-get update && apt-get install -y \
    jpegoptim optipng pngquant gifsicle \
    curl \
    supervisor \
    nano \
    locales \
    gnupg

RUN install-php-extensions intl exif bcmath bz2 gd

RUN curl -sSL https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor -o /usr/share/keyrings/postgresql.gpg && \
       echo "deb [signed-by=/usr/share/keyrings/postgresql.gpg] http://apt.postgresql.org/pub/repos/apt $(grep VERSION_CODENAME /etc/os-release | cut -d= -f2)-pgdg main" > /etc/apt/sources.list.d/pgdg.list && \
       apt-get update && apt-get install -y postgresql-client-17

RUN curl -fsSL https://deb.nodesource.com/setup_23.x | bash -
RUN apt-get install -y nodejs
RUN npm install -g npm@11.1.0 && npm install -g npm-check-updates
RUN chown -R ${UID}:${GID} /var/www
RUN chmod 777 -R /var/www

USER ${USER}

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

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
