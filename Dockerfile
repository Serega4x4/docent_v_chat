FROM php:8.3-fpm

# Установка зависимостей и необходимых расширений PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка рабочей директории
WORKDIR /var/www

# Копирование файлов проекта
COPY . .

# Установка зависимостей Composer
RUN composer install --optimize-autoloader --no-dev

# Копирование .env файла
COPY .env .env

# Установка прав доступа
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Оптимизация Laravel для продакшена
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Запуск PHP-FPM
CMD ["php-fpm"]