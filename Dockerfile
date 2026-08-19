FROM php:8.2-apache

# Install PDO MySQL extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable mod_rewrite and enforce prefork
RUN rm -rf /etc/apache2/mods-enabled/mpm* \
    && ln -s /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/ \
    && ln -s /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/ \
    && a2enmod rewrite

# Allow .htaccess overrides
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copy source
COPY src/ /var/www/html/
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

EXPOSE 80

# Source envvars and run apache foreground directly
CMD ["/bin/bash", "-c", "source /etc/apache2/envvars && exec apache2 -DFOREGROUND"]