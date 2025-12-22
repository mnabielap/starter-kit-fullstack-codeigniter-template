# Use PHP 8.2 with Apache
FROM php:8.2-apache

# 1. Install system dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libsqlite3-dev \
    unzip \
    zip \
    zlib1g-dev \
    libzip-dev \
    && docker-php-ext-install intl pdo pdo_mysql pdo_sqlite mysqli zip \
    && a2enmod rewrite

# 2. Configure Apache Port to 5005
RUN sed -i 's/Listen 80/Listen 5005/' /etc/apache2/ports.conf && \
    sed -i 's/:80/:5005/' /etc/apache2/sites-available/000-default.conf

# 3. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Set working directory
WORKDIR /var/www/html

# 5. Copy application files
COPY . .

# 6. Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# 7. Set Permissions
RUN chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 775 /var/www/html/writable \
    && chown -R www-data:www-data /var/www/html/public \
    && chmod -R 775 /var/www/html/public

# 8. Configure Apache DocumentRoot AND Enable .htaccess
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# --- ALLOW OVERRIDE ALL ---
RUN echo "<Directory /var/www/html/public>" >> /etc/apache2/apache2.conf && \
    echo "    Options Indexes FollowSymLinks" >> /etc/apache2/apache2.conf && \
    echo "    AllowOverride All" >> /etc/apache2/apache2.conf && \
    echo "    Require all granted" >> /etc/apache2/apache2.conf && \
    echo "</Directory>" >> /etc/apache2/apache2.conf

# 9. Copy Entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# 10. Expose Port
EXPOSE 5005

# 11. Run
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]