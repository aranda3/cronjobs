# Imagen base con Apache y PHP
FROM php:8.2-apache

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar archivos al contenedor
COPY . /var/www/html/

# Establecer permisos
RUN chown -R www-data:www-data /var/www/html

# Configurar el .htaccess para rutas limpias si usas FastRoute
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
</Directory>' >> /etc/apache2/apache2.conf

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar dependencias (si tienes composer.json)
WORKDIR /var/www/html
RUN if [ -f "composer.json" ]; then composer install; fi
