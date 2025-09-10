#!/bin/bash

# 1. Install Composer dependencies
composer install --optimize-autoloader --no-dev

# 2. Generate Laravel's optimized files
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 3. Build front-end assets
npm install
npm run build

# 4. Run database migrations
php artisan migrate --force