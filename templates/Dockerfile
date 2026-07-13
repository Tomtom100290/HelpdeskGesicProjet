# PHP 8.2 (ou 8.3) et FPM
FROM php:8.2-fpm-alpine

# 2. Installation des dépendances système nécessaires (Linux Alpine)
RUN apk add --no-cache \
    bash \
    git \
    icu-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip

# 3. Installation et activation des extensions PHP requises pour Symfony & MySQL
RUN docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) \
    intl \
    pdo \
    pdo_mysql \
    zip

# 4. Récupération de Composer (le gestionnaire de paquets PHP) depuis l'image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Définition du dossier de travail à l'intérieur de la boîte Docker
WORKDIR /var/www/html

# 6. Configuration des variables d'environnement pour la production
ENV APP_ENV=prod
ENV COMPOSER_ALLOW_SUPERUSER=1

# 7. Copie des fichiers de gestion des dépendances en premier (Optimisation du cache Docker)
COPY composer.json composer.lock ./

# 8. Installation des dépendances Symfony SANS les outils de développement (plus léger, plus sécurisé)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# 9. Copie du reste du code de ton application de Helpdesk dans la boîte
COPY . .

# 10. Finalisation de l'autoloader Composer et exécution des scripts de build de Symfony
RUN composer dump-autoload --no-dev --optimize \
    && composer run-script prod-build

# 11. Sécurité : Ajustement des permissions pour que le serveur web puisse lire/écrire dans var/cache et var/log
RUN chown -R www-data:www-data /var/www/html/var

# 12. On indique que le conteneur écoute sur le port 9000 (le port standard de PHP-FPM)
EXPOSE 9000

# 13. Commande de démarrage par défaut de la boîte
CMD ["php-fpm"]