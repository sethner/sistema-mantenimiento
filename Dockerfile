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

# Configurar Nginx para Laravel (Optimizando la lectura de archivos estáticos e imágenes)
RUN echo 'server { \n\
    listen 8080; \n\
    root /var/www/html/public; \n\
    index index.php index.html; \n\
    location / { \n\
    try_files $uri $uri/ /index.php?$query_string; \n\
    } \n\
    location ~ \.php$ { \n\
    include fastcgi_params; \n\
    fastcgi_pass 127.0.0.1:9000; \n\
    fastcgi_index index.php; \n\
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \n\
    } \n\
    location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ { \n\
    expires max; \n\
    log_not_found off; \n\
    } \n\
    }' > /etc/nginx/sites-available/default

# Copiar el proyecto
COPY . /var/www/html
WORKDIR /var/www/html

# Instalar dependencias de Composer
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Permisos correctos para que Nginx pueda escribir y leer archivos/imágenes
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public

EXPOSE 8080

# Comando de inicio: Vincula storage de forma real antes de encender
CMD php artisan storage:link --force && php artisan migrate:fresh --seed --force && php-fpm -D && nginx -g "daemon off;"