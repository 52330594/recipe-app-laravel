FROM php:8.3-cli

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Install PHP dependencies & optimization
RUN composer install --no-dev --optimize-autoloader

# Setup SQLite database for live runtime
RUN touch /app/database/database.sqlite
RUN chmod -R 777 /app/storage /app/bootstrap/cache /app/database

EXPOSE 10000

# Run migrations and start artisan serve
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000