# ============================================================
# Stage 1: PHP-FPM with Laravel dependencies
# ============================================================
FROM php:8.2-fpm AS app

# Install system dependencies and PHP extensions commonly needed by Laravel
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application source code
COPY . .

# Install PHP dependencies (no dev dependencies, optimise autoloader)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Set correct permissions for Laravel storage and cache directories
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# ============================================================
# Stage 2: Production image — nginx + PHP-FPM + supervisord
# ============================================================
FROM php:8.2-fpm

# Install nginx, supervisord, and the same PHP extensions as stage 1
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    curl \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy built application from stage 1
COPY --from=app /var/www /var/www

# Copy nginx site config (replaces the default site)
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Copy supervisord config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Ensure nginx pid directory exists and log dirs are writable
RUN mkdir -p /run/nginx \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

WORKDIR /var/www

# Expose Railway's standard HTTP port
EXPOSE 8080

# Start nginx and PHP-FPM via supervisord
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
