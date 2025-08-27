<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // 1. Import View Facade
use Illuminate\Support\Facades\Auth; // 2. Import Auth Facade
use App\Models\Peminjaman;             // 3. Import model Peminjaman

class AppServiceProvider extends ServiceProvider
{
    // ... method register() ...

    public function boot(): void
    {
        // Bagikan data notifikasi secara global ke partial navbar admin
        View::composer('layouts.partials.admin-navbar', function ($view) {
            // Cek dulu apakah user sudah login dan merupakan admin untuk efisiensi
            if (Auth::check() && Auth::user()->admin) {
                $notificationCount = Peminjaman::where('status', 'Menunggu Konfirmasi')
                                               ->orWhere('status', 'Tunggu Konfirmasi Admin')
                                               ->count();
                // Kirim variabel $notificationCount ke view
                $view->with('notificationCount', $notificationCount);
            }
        });
    }
}