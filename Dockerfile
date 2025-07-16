FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy all files from the build context (your GitHub repo root) into /var/www/html
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html
