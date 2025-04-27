FROM php:8.3-apache

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

# Copier le projet
COPY . /var/www/html/

# Copier la config Apache
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Changer uniquement le DocumentRoot
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && a2enmod rewrite \
    && chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

# Installer dépendances
RUN composer install --optimize-autoloader

EXPOSE 80

CMD ["apache2-foreground"]
