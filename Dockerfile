# Chọn base image của PHP
FROM php:7.4-apache

# Cài đặt Composer (quản lý các thư viện PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Cài đặt các phần mềm cần thiết
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql

# Copy mã nguồn vào thư mục của Apache
COPY . /var/www/html/

# Cài đặt các thư viện PHP thông qua Composer
WORKDIR /var/www/html/
RUN composer install

# Mở cổng 80
EXPOSE 80

# Khởi chạy Apache
CMD ["apache2-foreground"]
