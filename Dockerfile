FROM php:8.1-apache

# Configurar variables para reducir el tamaño de la imagen
ENV DEBIAN_FRONTEND=noninteractive

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    intl \
    mysqli \
    pdo_mysql \
    zip \
    gd

# Optimizar PHP
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'upload_max_filesize=64M'; \
    echo 'post_max_size=64M'; \
    echo 'memory_limit=256M'; \
    echo 'max_execution_time=600'; \
    echo 'max_input_vars=3000'; \
    echo 'date.timezone=UTC'; \
} > /usr/local/etc/php/conf.d/custom-php.ini

# Habilitar mod_rewrite y headers para Apache
RUN a2enmod rewrite headers

# Configurar el directorio de trabajo
WORKDIR /var/www/html

# Copiar el script de inicio primero para aprovechar el caché de Docker
COPY coolify-init.sh /var/www/html/
RUN chmod +x /var/www/html/coolify-init.sh

# Copiar el código de la aplicación
COPY . /var/www/html/

# Establecer permisos correctos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && find /var/www/html -type d -name "uploads" -exec chmod -R 777 {} \; \
    && mkdir -p /var/www/html/logs \
    && chmod -R 777 /var/www/html/logs

# Exponer el puerto 80
EXPOSE 80

# Comando para iniciar Apache
CMD ["./coolify-init.sh"]
