# Use PHP 8.4 (lock file deps don't support 8.5 yet)
FROM php:8.4-apache

# Only one MPM must be loaded (prefork is required for mod_php)
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true && a2enmod mpm_prefork

# Install system deps for intl, gd
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    --no-install-recommends && rm -rf /var/lib/apt/lists/*

# Enable PHP extensions required by CakePHP and mpdf
RUN docker-php-ext-configure intl && \
    docker-php-ext-install -j$(nproc) intl gd pdo_mysql opcache

# Enable Apache mod_rewrite for CakePHP
RUN a2enmod rewrite headers

# Document root = CakePHP webroot
ENV APACHE_DOCUMENT_ROOT /var/www/html/webroot
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

# Copy app (use .dockerignore to exclude vendor, node_modules, etc.)
COPY . .

# Install PHP deps (no scripts to avoid app init that may need DB)
RUN composer install --optimize-autoloader --no-scripts --no-interaction

# Run CakePHP post-install (optional; remove if it fails without DB)
RUN composer run-script post-install-cmd --no-interaction || true

# Allow .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Railway uses PORT; entrypoint makes Apache listen on it
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
ENTRYPOINT ["docker-entrypoint.sh"]
