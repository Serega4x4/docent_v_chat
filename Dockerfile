FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
        git unzip zip curl libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копируем проект
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

RUN a2enmod rewrite
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
RUN composer install --optimize-autoloader --no-dev

# RUN php artisan route:cache

EXPOSE 80
CMD ["apache2-foreground"]
