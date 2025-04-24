FROM php:7.4-apache

# Cài Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Cài extension + git + unzip + zip để composer hoạt động
RUN apt-get update && apt-get install -y \
    unzip zip git curl libzip-dev && \
    docker-php-ext-install zip pdo pdo_mysql

# Copy mã nguồn vào container
COPY . /var/www/html/

# Set working dir
WORKDIR /var/www/html/

# Cài thư viện PHP
RUN composer install --no-interaction --prefer-dist

# Mở cổng 80
EXPOSE 80

# Chạy Apache
CMD ["apache2-foreground"]
