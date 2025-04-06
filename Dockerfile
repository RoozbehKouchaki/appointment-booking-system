# Use the official PHP Apache image
FROM php:8.4-apache

# Install PDO MySQL extension
RUN docker-php-ext-install pdo_mysql

# Set Apache DocumentRoot to your public folder
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Update Apache configuration to use the new DocumentRoot
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}/!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Enable mod_rewrite if needed
RUN a2enmod rewrite

# Copy your project files into the container
COPY . /var/www/html

# Set ownership
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]