FROM php:7.4-apache

# Cài Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Cài git, unzip, zip, libzip để Composer không lỗi
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    && docker-php-ext-install zip pdo

# Set thư mục làm việc
WORKDIR /var/www/html/

# Copy code vào image
COPY . .

# Cài thư viện PHP qua Composer
RUN composer install --no-interaction --prefer-dist --no-dev

EXPOSE 80

# Chạy Apache
CMD ["apache2-foreground"]
