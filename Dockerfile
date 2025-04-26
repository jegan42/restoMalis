# Choisir une image PHP avec Apache
FROM php:8.1-apache

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y --no-install-recommends \
    libfreetype6-dev \
    libicu-dev \
    libjpeg-dev \
    libpng-dev \
    libpq-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install intl zip pdo pdo_mysql pdo_pgsql

# Installer Composer (gestionnaire de dépendances PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier le projet dans le container
COPY . /var/www/html/

# Modifier le DocumentRoot d'Apache pour pointer sur /public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf && \
# Définir les permissions correctes pour les fichiers du projet
    if [ -d /var/www/html/var ]; then chown -R www-data:www-data /var/www/html/var; fi && \
    chown -R www-data:www-data /var/www/html/public && \
    a2enmod rewrite

# Exposer le port 80
EXPOSE 80

# Commande de démarrage de l'application
CMD ["apache2-foreground"]
