FROM php:8.2-fpm-alpine

# Instala las dependencias del sistema necesarias para la extensión GD 
RUN apk add --no-cache \
    font-dejavu \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev

# Configura, instala y habilita la extensión GD de PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

#Instala opcache para mejorar el rendimiento
RUN docker-php-ext-install opcache
