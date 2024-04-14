FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libpq-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    libzip-dev \
    libonig-dev \
    zip \
    jpegoptim optipng pngquant gifsicle \
    unzip \
    curl \
    supervisor \
    nodejs \
    npm

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring zip exif pcntl bcmath gd

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY ./docker/php-fpm.conf /usr/local/etc

RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash

WORKDIR /var/www

COPY . /var/www

RUN if [ ! -e ".env" ]; then cp .env.example .env ; fi
RUN composer install --optimize-autoloader --no-interaction --no-progress

EXPOSE 9000
COPY ./docker/supervisord-app.conf /etc/supervisor/conf.d/supervisord-app.conf
COPY ./docker/supervisord-artisan.conf /etc/supervisor/conf.d/supervisord-artisan.conf

CMD ["supervisord", "-n"]
