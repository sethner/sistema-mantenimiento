FROM php:8.2-fpm

# Instalar Nginx y dependencias del sistema
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar Nginx ampliando el límite de subida (client_max_body_size) y agregando buffers
RUN echo 'server { \n\
    listen 8080; \n\
    root /var/www/html/public; \n\
    index index.php index.html; \n\
    \n\
    # PERMITIR SUBIDA DE IMÁGENES GRANDES \n\
    client_max_body_size 64M; \n\
    \n\
    # Corrección de Buffers para evitar caídas de peticiones HTTP2 \n\
    fastcgi_buffers 16 16k; \n\
    fastcgi_buffer_size 32k; \n\
    proxy_buffer_size 128k; \n\
    proxy_buffers 4 256k; \n\
    proxy_busy_buffers_size 256k; \n\
    \n\
    location / { \n\
    try_files $uri $uri/ /index.php?$query_string; \n\
    } \n\
    location ~ \.php$ { \n\
    include fastcgi_params; \n\
    fastcgi_pass 127.0.0.1:9000; \n\
    fastcgi_index index.php; \n\
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \n\
    } \n\
    }' > /etc/nginx/sites-available/default

# Ajustar los límites de subida directamente en la configuración interna de PHP
RUN echo "upload_max_filesize = 64M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini

# Copiar el proyecto
COPY . /var/www/html
WORKDIR /var/www/html

# Instalar dependencias de Composer
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Permisos correctos para Storage, Caché y carpeta Pública para que se puedan escribir imágenes
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public

EXPOSE 8080

# Comando de inicio: Recrea el enlace simbólico y corre los servicios
CMD php artisan storage:link --force && php artisan migrate --force && php-fpm -D && nginx -g "daemon off;"