FROM php:8.2-fpm

# Instalar dependências do sistema, PHP e extensões
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    zip unzip curl git libzip-dev libonig-dev curl \
    && docker-php-ext-install pdo_mysql mbstring zip \
    && pecl install xdebug && docker-php-ext-enable xdebug

# Instalar Node.js e npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Copiar composer (sem alteração)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY .docker/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /var/www

COPY . .

# Instalar dependências PHP
RUN composer install

# Instalar dependências JS e build dos assets Vite
RUN npm install
RUN npm run build

CMD php artisan serve --host=0.0.0.0 --port=8000