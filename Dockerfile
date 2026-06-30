FROM php:8.2-apache

# 1. Instalar dependencias del sistema y PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# 2. Activar módulo rewrite para Laravel
RUN a2enmod rewrite

# 3. Traer e instalar Composer oficial de forma global
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Forzar de forma absoluta la raíz en la configuración global de Apache
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/apache2.conf
RUN sed -i 's|<Directory /var/www/>|<Directory /var/www/html/public/>|g' /etc/apache2/apache2.conf

# 5. Sobreescribir por completo el sitio por defecto con la ruta pública
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks MultiViews\n\
    AllowOverride All\n\
    Require all granted\n\
    </Directory>\n\
    </VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# 6. Configurar directorio de trabajo y copiar el código
WORKDIR /var/www/html
COPY . .

# 7. Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 8. Crear un archivo de prueba rápido por si acaso
RUN echo "<?php echo '¡El contenedor y Apache funcionan perfectamente!'; ?>" > public/prueba.php

# 9. Permisos absolutos para evitar bloqueos de almacenamiento
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD php artisan storage:link --force && php artisan config:cache && php artisan view:cache && apache2-foreground