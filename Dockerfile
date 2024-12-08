FROM php:8.1-fpm

RUN apt-get update && apt-get upgrade -y
RUN docker-php-ext-install mysqli pdo_mysql bcmath

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY . /var/www/html

RUN npm install && npm run build

RUN mkdir -p /var/www/html/var/log && \
    chown -R www-data:www-data /var/www/html/var/log && \
    chmod -R 775 /var/www/html/var/log