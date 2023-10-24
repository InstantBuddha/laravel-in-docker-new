# laravel-in-docker-new

Enginx can be reached through http://localhost and not the original ip mentioned. However, this can be changed in \laravel\config\app.php

I added MySQL extension:
```bash
docker exec -it laravel-in-docker-new-app-1 bash
docker-php-ext-install pdo pdo_mysql
```

AND after that I could run
```bash
docker exec -it laravel-in-docker-new-app-1 bash
php artisan migrate
```