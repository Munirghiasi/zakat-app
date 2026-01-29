FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git unzip zip libsqlite3-dev

# Enable rewrite
RUN a2enmod rewrite

# 🔥 VERY IMPORTANT: set document root to public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN chmod -R 777 storage bootstrap/cache

CMD php artisan migrate --force && apache2-foreground
