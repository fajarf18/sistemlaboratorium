#!/bin/bash

# Exit immediately if a command exits with a non-zero status.
set -e

# HAPUS CACHE LAMA (PENTING!)
# Pastikan direktori ada sebelum mencoba menghapus file
if [ -f "bootstrap/cache/config.php" ]; then
    php artisan config:clear
fi
if [ -f "bootstrap/cache/routes.php" ]; then
    php artisan route:clear
fi
if [ -d "storage/framework/views" ]; then
    php artisan view:clear
fi
if [ -f "bootstrap/cache/events.php" ]; then
    php artisan event:clear
fi

# 1. Install Composer dependencies
composer install --optimize-autoloader --no-dev

# 2. Link storage directory
php artisan storage:link

# 3. BUAT CACHE BARU
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 4. Install NPM dependencies and build assets
npm install
npm run build

# 5. Run database migrations
php artisan migrate --force