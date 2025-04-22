FROM php:8.2-fpm

# Install required packages
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    default-mysql-client \
    libzip-dev \
    && docker-php-ext-configure pdo_mysql \
    && docker-php-ext-install pdo_mysql

# Install PHP extensions
RUN docker-php-ext-install mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create directories and set permissions
RUN mkdir -p /var/www/kodano-task && \
    mkdir -p /var/www/.composer/cache && \
    chown -R www-data:www-data /var/www/kodano-task && \
    chown -R www-data:www-data /var/www/.composer

# Set working directory
WORKDIR /var/www/kodano-task 