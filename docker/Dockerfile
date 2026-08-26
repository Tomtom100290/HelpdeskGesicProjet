# syntax=docker/dockerfile:1

##############################################
# Stage 1 : dependances Composer
##############################################
FROM composer:2 AS composer

##############################################
# Stage 2 : image PHP-FPM pour Symfony
##############################################
FROM php:8.3-fpm-bookworm AS symfony_php

# Arguments configurables au build
ARG APP_ENV=prod
ENV APP_ENV=${APP_ENV} \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/tmp/composer

WORKDIR /var/www/html

# --- Dependances systeme + extensions PHP necessaires a Symfony ---
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libxml2-dev \
    libpq-dev \
    acl \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
    pdo \
    pdo_mysql \
    mysqli \
    intl \
    zip \
    gd \
    opcache \
    xml \
    soap \
    bcmath \
    && pecl install apcu redis \
    && docker-php-ext-enable apcu redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# --- Copie de Composer depuis le stage officiel ---
COPY --from=composer /usr/bin/composer /usr/bin/composer

# --- Configuration PHP recommandee pour Symfony en prod ---
COPY docker/php.ini /usr/local/etc/php/conf.d/symfony.ini
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# --- Copie du code source ---
COPY . .

# --- Installation des dependances PHP (prod, sans dev, optimise autoload) ---
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    && composer dump-autoload --optimize --no-dev --classmap-authoritative

# --- Droits sur var/ (cache, logs) pour www-data ---
RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var \
    && chmod -R 775 var

# --- Valeurs FACTICES, utilisees UNIQUEMENT pour permettre au kernel Symfony
#     de booter pendant la compilation des assets. Jamais utilisees en
#     runtime : ecrasees par les vraies variables d'environnement du conteneur
#     (voir docker-compose.prod.yml). Evite qu'un secret de prod ne soit
#     grave dans les layers de l'image (principe 12-factor app).
ENV APP_SECRET=build_time_dummy_secret \
    DATABASE_URL="mysql://user:pass@127.0.0.1:3306/db?serverVersion=mariadb-11.0.0&charset=utf8mb4"

# --- Compilation du CSS Tailwind ---
RUN php bin/console tailwind:build --minify

# --- Compilation des assets AssetMapper (si utilisé) ---
RUN php bin/console asset-map:compile --env=prod

USER www-data

EXPOSE 9000

CMD ["php-fpm"]