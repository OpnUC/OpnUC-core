#!/bin/sh

cd /var/www
composer install
cp .env.example .env

sed -i -e 's/^APP_DEBUG=.*$/APP_DEBUG=false/' .env

sed -i -e 's/^DB_HOST=.*$/DB_HOST=mysql/' .env
sed -i -e 's/^DB_DATABASE=.*$/DB_DATABASE=opnuc/' .env
sed -i -e 's/^DB_USERNAME=.*$/DB_USERNAME=opnuc/' .env
sed -i -e 's/^DB_PASSWORD=.*$/DB_PASSWORD=opnuc/' .env

sed -i -e 's/^CACHE_DRIVER=.*$/CACHE_DRIVER=array/' .env

sed -i -e 's/^REDIS_HOST=.*$/REDIS_HOST=redis/' .env

php artisan key:generate
php artisan jwt:generate
php artisan migrate
php artisan db:seed

chmod -R 777 /var/www/storage
