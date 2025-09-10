#!/bin/bash

echo "✅ Build script started"

# 1. Install Composer dependencies
echo "➡️ Installing Composer dependencies..."
composer install --optimize-autoloader --no-interaction --no-progress --no-dev

# 2. Generate Laravel's optimized files
echo "➡️ Caching Laravel configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 3. Build front-end assets
echo "➡️ Building front-end assets..."
npm install
npm run build

# 4. PENTING: Pindahkan direktori storage ke /tmp
# Vercel hanya mengizinkan penulisan ke direktori /tmp
echo "➡️ Moving storage directories to /tmp..."
mv storage/framework /tmp/framework
mv storage/logs /tmp/logs

# 5. Buat symlink untuk storage link
# Ini adalah pengganti `php artisan storage:link`
echo "➡️ Creating storage symlink..."
rm -rf public/storage
ln -s `realpath storage/app/public` public/storage

echo "✅ Build script finished successfully"