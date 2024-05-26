FROM php:8.3-fpm AS base

ARG UID
ARG GID
ARG USER

ENV UID=${UID}
ENV GID=${GID}
ENV USER=${USER}

WORKDIR /var/www/
RUN addgroup --gid ${GID} --system ${USER}
RUN adduser --system --home /home/${USER} --shell /bin/sh --uid ${UID} --ingroup ${USER} ${USER}

RUN sed -i "s/user = www-data/user = '${USER}'/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = '${USER}'/g" /usr/local/etc/php-fpm.d/www.conf
RUN echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libpq-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    libbz2-dev \
    libsodium-dev \
    zlib1g-dev \
    libzip-dev \
    libonig-dev \
    libicu-dev \
    zip \
    jpegoptim optipng pngquant gifsicle \
    unzip \
    curl \
    nano \
    supervisor

RUN mkdir -p /usr/share/postgresql-common/pgdg && \
    curl -o /usr/share/postgresql-common/pgdg/apt.postgresql.org.asc --fail https://www.postgresql.org/media/keys/ACCC4CF8.asc && \
    install -d /usr/share/postgresql-common/pgdg

RUN sh -c 'echo "deb [signed-by=/usr/share/postgresql-common/pgdg/apt.postgresql.org.asc] https://apt.postgresql.org/pub/repos/apt bookworm-pgdg main" > /etc/apt/sources.list.d/pgdg.list'

RUN apt update && apt install -y postgresql-16

RUN pecl install redis \
    && docker-php-ext-enable redis

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring zip exif pcntl bcmath gd bz2 sodium zip intl

RUN curl -fsSL https://deb.nodesource.com/setup_21.x | bash -
RUN apt-get install -y nodejs
RUN npm install -g npm@10.8.0

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
