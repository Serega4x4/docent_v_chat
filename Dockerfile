FROM richarvey/nginx-php-fpm:3.1.6

WORKDIR /var/www

COPY . .

RUN composer install --optimize-autoloader --no-dev

RUN mkdir -p /var/www/storage/logs \
    && chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

RUN php artisan config:clear \
    && php artisan config:cache \
    && php artisan route:cache

ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]