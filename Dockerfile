FROM php:8.4-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo_pgsql zip

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Рабочая директория
WORKDIR /var/www/html

# Копируем файлы проекта
COPY . .

# Устанавливаем зависимости PHP
RUN composer install --no-interaction --optimize-autoloader

# Права на папки
RUN chown -R www-data:www-data /var/www/html/var /var/www/html/public

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
