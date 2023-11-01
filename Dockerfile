FROM composer:2.6.5

WORKDIR /app

# Copy the Laravel application files into the container
COPY . /app

RUN docker-php-ext-install pdo pdo_mysql

CMD ["php", "artisan", "serve", "--host=0.0.0.0"]    
