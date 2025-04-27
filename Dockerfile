FROM php:8.3-apache

# Installer PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    libfreetype6-dev \
    libicu-dev \
    libjpeg-dev \
    libpng-dev \
    libpq-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install intl zip pdo pdo_mysql pdo_pgsql \
    && which composer || curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Config Apache
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Activer mod_rewrite
RUN a2enmod rewrite

# Copier code Symfony
COPY . /var/www/html/

# Donner les bonnes permissions
RUN chown -R www-data:www-data /var/www/html

# Installer les dépendances PHP
WORKDIR /var/www/html/
RUN composer install --optimize-autoloader --no-dev

# Compiler le cache Symfony
RUN php bin/console cache:clear --no-warmup --env=prod
RUN php bin/console cache:warmup --env=prod

# Fix permissions cache & log
RUN chown -R www-data:www-data /var/www/html/var /var/www/html/vendor

EXPOSE 80

CMD ["apache2-foreground"]
