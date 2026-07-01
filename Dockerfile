FROM php:8.2-apache

# Configurar variables de entorno del sistema para forzar MPM Prefork antes de instalar nada
ENV APACHE_RUN_USER=www-data \
    APACHE_RUN_GROUP=www-data \
    APACHE_LOG_DIR=/var/www/html \
    APACHE_MPM=prefork

# Instalar herramientas del sistema y extensiones de PHP necesarias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Instalar Composer de forma oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Habilitar el módulo de reescritura de Apache para Laravel
RUN a2enmod rewrite

# SOLUCIÓN DEFINITIVA MPM: Forzar la carga exclusiva de mpm_prefork eliminando mpm_event físicamente
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_event.conf || true \
    && ln -s /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load || true

# Configurar la carpeta pública de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Copiar el código del proyecto al contenedor
COPY . /var/www/html

# Instalar las dependencias de Composer
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Dar permisos a las carpetas de almacenamiento de Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Puerto en el que escuchará Apache
EXPOSE 8080

# Comando de inicio: Migraciones + encendido seguro del servidor Apache
CMD php artisan migrate:fresh --seed --force && apache2-foreground



