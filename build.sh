#!/bin/bash

# 1. Install Composer dependencies
composer install --optimize-autoloader --no-interaction --no-progress --no-dev

# 2. Generate Laravel's optimized files
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 3. Build front-end assets
npm install
npm run build

# 4. PENTING: Pindahkan direktori storage yang dibutuhkan ke /tmp
# Vercel hanya mengizinkan penulisan ke direktori /tmp
mv storage/framework /tmp/framework
mv storage/logs /tmp/logs

# 5. Buat symlink untuk storage link
# Ini adalah pengganti `php artisan storage:link`
rm -rf public/storage
ln -s `realpath storage/app/public` public/storage