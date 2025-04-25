# Choisir une image PHP avec Apache
FROM php:8.1-apache

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Installer Composer (gestionnaire de dépendances PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier le projet dans le container
COPY . /var/www/html/

# Définir les permissions correctes pour les fichiers du projet
RUN chown -R www-data:www-data /var/www/html/var /var/www/html/vendor /var/www/html/public && \
	a2enmod rewrite

# Exposer le port 80
EXPOSE 80

# Commande de démarrage de l'application
CMD ["apache2-foreground"]
