# Use the official Composer image as the base image
#Ne latest legyen
FROM composer:latest

# Set the working directory to /app
WORKDIR /app

# Copy the Laravel application files into the container
COPY . /app

RUN docker-php-ext-install pdo pdo_mysql

# Expose port 8000 (the port used by Laravel's artisan serve)
# Ezt lehet megtartani és a a 18 soron lévő port törölhető
EXPOSE 8000

# Start the Laravel application with the specified command
# a 8000 max akkor felesleges, ha ez a default érték, úgy tűnik az
#elvbileg a serve után minden fölö9sleges
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]    
