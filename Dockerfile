# Étape 1 : Builder
FROM php:8.2-cli AS builder

# Installer les extensions nécessaires
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpq-dev \
    && docker-php-ext-install zip pdo_mysql

# Copier les fichiers du projet
WORKDIR /var/www/symfony
COPY . .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
RUN composer --version

# Installer Composer
#COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installer les dépendances avec Composer
#RUN COMPOSER_ALLOW_SUPERUSER=1 composer install 
#RUN composer install 
# Étape 2 : Image finale
FROM php:8.2-fpm


# Installer les extensions nécessaires
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpq-dev \
    && docker-php-ext-install zip pdo_mysql

# Copier les fichiers du projet
WORKDIR /var/www/symfony
COPY . .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
RUN composer --version

# Installer Composer
#COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier les fichiers depuis le builder
COPY --from=builder /var/www/symfony /var/www/symfony

# Configuration des permissions
WORKDIR /var/www/symfony
RUN chown -R www-data:www-data /var/www/symfony

# Exposer le port
EXPOSE 9000

# Commande par défaut
CMD ["php-fpm"]