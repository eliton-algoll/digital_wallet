FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev unzip git curl libpng-dev libonig-dev libxml2-dev \
  && docker-php-ext-install zip pdo_mysql mbstring exif pcntl bcmath gd \
  && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY ../composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress || true

COPY .. .

RUN chown -R www-data:www-data /var/www \
  && find storage bootstrap/cache -type d -exec chmod 775 {} \; \
  && find storage bootstrap/cache -type f -exec chmod 664 {} \;

RUN docker-php-ext-enable opcache || true

EXPOSE 9000

CMD ["php-fpm"]
