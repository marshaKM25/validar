FROM php:8.2-fpm-alpine

# Instala las dependencias del sistema, INCLUYENDO ImageMagick
RUN apk add --no-cache \
    font-dejavu \
    imagemagick-dev \
    imagemagick \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev

# Instala la extensión GD (sigue siendo útil para algunas operaciones)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# CORRECCIÓN: Instala las herramientas de construcción necesarias para compilar extensiones
# Creamos un paquete virtual ".build-deps" para poder eliminarlo fácilmente después.
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apk del .build-deps # Limpiamos las herramientas de construcción que ya no necesitamos

# Instala opcache para mejorar el rendimiento
RUN docker-php-ext-install opcache
