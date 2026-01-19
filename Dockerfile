FROM php:8.3-cli

# System deps
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libsqlite3-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    postgresql-client \
    && docker-php-ext-install pdo pdo_pgsql pdo_sqlite \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable PHP error reporting for dev
RUN echo "display_errors=On" > /usr/local/etc/php/conf.d/docker-php-errors.ini \
    && echo "display_startup_errors=On" >> /usr/local/etc/php/conf.d/docker-php-errors.ini \
    && echo "error_reporting=E_ALL" >> /usr/local/etc/php/conf.d/docker-php-errors.ini

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-interaction --prefer-dist

EXPOSE 8000

# Default command: run Laravel artisan serve
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
