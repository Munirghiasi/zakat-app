FROM php:8.2-apache

RUN apt-get update && apt-get install -y git unzip sqlite3 libsqlite3-dev
RUN a2enmod rewrite
RUN docker-php-ext-install pdo pdo_sqlite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev 

RUN touch database/database.sqlite

# 🔥 STRONG PERMISSION FIX
RUN chmod -R 777 storage bootstrap/cache database

RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80
CMD ["apache2-foreground"]

RUN touch database/database.sqlite
RUN chmod -R 777 storage bootstrap/cache database


RUN php artisan migrate
