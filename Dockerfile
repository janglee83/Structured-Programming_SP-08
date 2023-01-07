FROM php:fpm

# Arguments defined in docker-compose.yml
ARG user=tung
ARG uid=1000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    unzip

# Install pgsql
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
#RUN docker-php-ext-install mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . /var/www/code
# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user \
    && chown -R 33:33 . && \
    chmod -R 777 /var/www/code/storage && \
    chmod -R 777 /var/www/code/bootstrap
# Set working directory
WORKDIR /var/www/code

USER $user
