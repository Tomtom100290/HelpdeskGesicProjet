# PHP 8.2 et FPM
FROM php:8.2-fpm-alpine

# 2. Installation des dépendances système nécessaires + Node.js/npm
RUN apk add --no-cache \
    bash \
    git \
    icu-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm

# 3. Installation et activation des extensions PHP requises
RUN docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) \
    intl \
    pdo \
    pdo_mysql \
    zip

# 4. Récupération de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Définition du dossier de travail
WORKDIR /var/www/html

# 6. Argument de build
ARG APP_ENV=prod
ENV APP_ENV=${APP_ENV}
ENV COMPOSER_ALLOW_SUPERUSER=1

# 7. Copie des fichiers de dépendances
COPY composer.json composer.lock ./

# 8. Installation des dépendances
RUN if [ "$APP_ENV" = "prod" ]; then \
    composer install --no-dev --no-scripts --no-autoloader --prefer-dist; \
    else \
    composer install --no-scripts --no-autoloader --prefer-dist; \
    fi

# 9. Copie du reste du code
COPY . .

# 10. Finalisation de l'autoloader
RUN if [ "$APP_ENV" = "prod" ]; then \
    composer dump-autoload --no-dev --optimize && composer run-script prod-build; \
    else \
    composer dump-autoload --optimize; \
    fi

# 11. Ajustement des permissions
RUN chown -R www-data:www-data /var/www/html/var

EXPOSE 9000

CMD ["php-fpm"]