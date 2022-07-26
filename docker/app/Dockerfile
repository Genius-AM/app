FROM php:7.1.30-fpm-alpine3.10

# Copy project
WORKDIR /var/www/html/
COPY --chown=www-data:www-data . .

# Install system deps
RUN apk add --update --no-cache git \
                                curl \
                                libpng-dev \
                                oniguruma-dev \
                                libxml2-dev \
                                zip \
                                unzip \
                                mc \
                                nodejs \
                                npm
# Clear cache
RUN rm -rf /var/cache/apk/*

# Install php extensions
RUN docker-php-ext-install pdo_mysql \
                           mbstring \
                           exif \
                           pcntl \
                           bcmath \
                           gd

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html/
USER www-data:www-data

# Npm configuration
ARG BUILD_CONFIGURATION
RUN npm i
RUN npm run ${BUILD_CONFIGURATION}

RUN composer install --ignore-platform-req=ext-zip \
                     --no-scripts \
                     --prefer-dist
RUN composer update --ignore-platform-req=ext-zip
