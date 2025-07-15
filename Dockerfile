FROM php:8.2-apache

RUN a2enmod rewrite

# Copy your code into the Apache directory
COPY . C:\xampp\htdocs\Rajashree Ent\Enterprise\pages

# Set working directory
WORKDIR C:\xampp\htdocs\Rajashree Ent\Enterprise\pages	

# Expose port 80
EXPOSE 80
