# Use the official Composer image as the base image
FROM composer:latest

# Set the working directory to /app
WORKDIR /app

# Copy the Laravel application files into the container
COPY . /app

RUN docker-php-ext-install pdo pdo_mysql

# Expose port 8000 (the port used by Laravel's artisan serve)
EXPOSE 8000

# Start the Laravel application with the specified command
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
