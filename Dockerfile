# Используем официальный образ PHP с Apache
FROM php:8.2-apache

# Устанавливаем системные зависимости
RUN apt-get update && apt-get install -y \
    git unzip zip curl libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip gd

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копируем Laravel-проект
COPY . /var/www/html

# Настройка прав доступа
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Включаем mod_rewrite
RUN a2enmod rewrite

# Настраиваем Apache
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

# Установка зависимостей Laravel
RUN composer install --optimize-autoloader --no-dev

# Запускаем оптимизацию Laravel
RUN php artisan config:cache && php artisan route:cache

EXPOSE 80
