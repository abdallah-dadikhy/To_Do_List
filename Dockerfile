# Use a base image with PHP and Nginx
FROM phpdockerio/php8.2-fpm:latest

# Set working directory
WORKDIR /app

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \ # For PostgreSQL, remove if only using MySQL
    libzip-dev \
    # Add any other dependencies your application might need, e.g., imagemagick, libpng-dev
    && docker-php-ext-install pdo_mysql zip # Add pdo_pgsql if using PostgreSQL

# Copy composer.lock and composer.json for efficient caching
COPY composer.json composer.lock ./

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy the rest of the application code
COPY . .

# Generate application key if not already present
RUN php artisan key:generate --force

# Run migrations and seeders (only if not in production and database is empty)
# In production, you might run these manually or via CI/CD pipeline
# RUN php artisan migrate --force
# RUN php artisan db:seed --force

# Set proper permissions for storage and bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Expose port (if your FPM is listening on a specific port, default is 9000)
EXPOSE 9000