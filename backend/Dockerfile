FROM php:8.2-fpm

# Instala extensiones necesarias
RUN apt-get update && apt-get install -y \
    git zip unzip libpq-dev libzip-dev libonig-dev \
    mariadb-client \
    && docker-php-ext-install pdo pdo_mysql

#Install xdebug
RUN pecl install xdebug-3.4.4 && docker-php-ext-enable xdebug

COPY docker/php.ini /usr/local/etc/php/

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copia el código fuente y el script de entrada
COPY . .
COPY entrypoint.sh /entrypoint.sh

# Da permisos de ejecución al entrypoint
RUN chmod +x /entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/entrypoint.sh"]
