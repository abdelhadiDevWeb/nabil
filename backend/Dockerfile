# FROM php:8.2-fpm

# # Arguments defined in docker-compose.yml
# ARG user=www
# ARG uid=1000

# # Install system dependencies
# RUN apt-get update && apt-get install -y \
#     git \
#     curl \
#     libpng-dev \
#     libonig-dev \
#     libxml2-dev \
#     zip \
#     unzip \
#     libpq-dev

# # Clear cache
# RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# # Install PHP extensions
# RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# # Get latest Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Create system user to run Composer and Artisan Commands
# RUN useradd -G www-data,root -u $uid -d /home/$user $user
# RUN mkdir -p /home/$user/.composer && \
#     chown -R $user:$user /home/$user

# # Set working directory
# WORKDIR /var/www

# # Copy project files
# # COPY ./backend /var/www
# COPY . /var/www

# # Change ownership of application files
# RUN chown -R $user:$user /var/www

# USER $user

FROM php:8.2-fpm

ARG user=www
ARG uid=1000

# تثبيت الأدوات و Nginx
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    nginx

# تثبيت امتدادات PHP
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# إنشاء مستخدم
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && chown -R $user:$user /home/$user

# إعداد مجلد العمل ونسخ المشروع
WORKDIR /var/www
COPY . /var/www
RUN chown -R $user:$user /var/www

# نسخ ملف إعدادات Nginx
COPY nginx.conf /etc/nginx/sites-available/default

USER $user

# تشغيل PHP-FPM وNginx معًا
CMD php-fpm -R & nginx -g 'daemon off;'
