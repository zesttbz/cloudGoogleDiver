FROM php:7.4-apache

# Cài Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Cài các phần mở rộng cần thiết
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev && \
    docker-php-ext-install pdo && \
    docker-php-ext-install pdo_mysql

# Copy mã nguồn
COPY . /var/www/html/

# Làm việc tại thư mục web root
WORKDIR /var/www/html

# Cài đặt thư viện PHP
RUN composer install

EXPOSE 80
CMD ["apache2-foreground"]
