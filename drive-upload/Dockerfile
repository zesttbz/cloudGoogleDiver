FROM php:7.4-apache

# Cài composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Cài zip, unzip, git, libzip
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    && docker-php-ext-install zip pdo

# Copy code vào container
WORKDIR /var/www/html/
COPY . .

# Cho phép composer chạy dưới quyền root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Cài thư viện PHP
RUN composer install --no-interaction --prefer-dist

# Mở port web
EXPOSE 80

# Khởi chạy Apache
CMD ["apache2-foreground"]
