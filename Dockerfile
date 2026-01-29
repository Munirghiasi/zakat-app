FROM php:8.2-apache

# Install system deps
RUN apt-get update && apt-get install -y \
    git unzip zip libsqlite3-dev

# Enable apache rewrite
RUN a2enmod rewrite

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

# permissions for laravel
RUN chmod -R 777 storage bootstrap/cache

# 🔥 THIS IS THE IMPORTANT PART (runtime, not build)
CMD php artisan migrate --force && apache2-foreground
