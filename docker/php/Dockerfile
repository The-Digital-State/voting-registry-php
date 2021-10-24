FROM php:8-fpm

# utils
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    nano \
    git \
    libicu-dev && docker-php-ext-configure intl && docker-php-ext-install intl \
    && rm -rf /var/lib/apt/lists/*

# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# mysql
RUN docker-php-ext-install pdo pdo_mysql

# xdebug
WORKDIR /app
#RUN pecl install xdebug && docker-php-ext-enable xdebug
#COPY ./docker/php/ext-xdebug.ini /usr/local/etc/php/conf.d/ext-xdebug.ini

COPY . /app
COPY .env.prod /app/.env
RUN composer update

RUN chmod 777 /app/storage/logs

RUN chmod +x pre-start.sh

ENTRYPOINT ["./pre-start.sh"]
