FROM php:8.2-fpm-alpine

WORKDIR /app

RUN docker-php-ext-install pdo pdo_mysql

CMD ["php", "artisan", "serve", "--host=0.0.0.0"]    
