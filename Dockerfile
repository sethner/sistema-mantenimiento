FROM php:8.2-apache

# Instalar extensiones de PHP necesarias para MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar el módulo de reescritura de Apache para Laravel
RUN a2enmod rewrite

# SOLUCIÓN CRÍTICA PARA MPM: Desactivar event y asegurar mpm_prefork
RUN a2dismod mpm_event || true
RUN a2enmod mpm_prefork || true

# Configurar la carpeta pública de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Copiar el código del proyecto al contenedor
COPY . /var/www/html

# Dar permisos a las carpetas de almacenamiento de Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Puerto en el que escuchará Apache (Railway pasará la variable PORT)
EXPOSE 8080

# Comando de inicio completo: Migraciones + encendido del servidor Apache
CMD php artisan migrate:fresh --seed --force && apache2-foreground