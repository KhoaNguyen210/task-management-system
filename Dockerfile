# Sử dụng hình ảnh PHP chính thức với Apache và các tiện ích mở rộng cần thiết
FROM php:8.2-apache

# Cài đặt các tiện ích mở rộng PHP cần thiết
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    jpegoptim optipng pngquant gifsicle \
    && rm -rf /var/lib/apt/lists/*

# Cài đặt các tiện ích mở rộng GD để xử lý ảnh
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Cài đặt các tiện ích mở rộng khác
RUN docker-php-ext-install pdo_mysql zip exif pcntl

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Thiết lập thư mục làm việc
WORKDIR /var/www/html

# Sao chép mã nguồn ứng dụng vào container
# Chúng ta sẽ sử dụng volume để đồng bộ code, nhưng vẫn cần copy ban đầu để tránh lỗi khi container khởi tạo
COPY . .

# Cấp quyền cho thư mục storage và bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Bật rewrite module cho Apache
RUN a2enmod rewrite

# Cấu hình Apache để sử dụng .htaccess
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf

# Expose port (mặc định Apache chạy trên port 80)
EXPOSE 80
