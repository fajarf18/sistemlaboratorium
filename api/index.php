<?php

// File: api/index.php

// Pindah satu direktori ke atas dari /api ke root proyek
define('LARAVEL_START', microtime(true));

// Muat autoloader yang dihasilkan Composer
require __DIR__.'/../vendor/autoload.php';

// Bootstrap aplikasi Laravel dan dapatkan instance aplikasi
$app = require_once __DIR__.'/../bootstrap/app.php';

// Buat kernel HTTP dari aplikasi
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Tangani permintaan HTTP yang masuk
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Kirim respons kembali ke browser
$response->send();

// Hentikan aplikasi
$kernel->terminate($request, $response);