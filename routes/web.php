<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Rute untuk tamu (landing page)
Route::get('/', function () {
    return redirect()->route('login');
});

// Rute pengarah setelah login
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    if ($user->role === 'user') {
        return redirect()->route('user.dashboard');
    }
    return redirect('/');
})->middleware(['auth', 'verified'])->name('dashboard');

// Grup rute khusus Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard'); 
    })->name('dashboard');
});

// Grup rute khusus User (Mahasiswa)
Route::middleware('auth')->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', function () {
        return view('user.dashboard'); 
    })->name('dashboard');
    // ... rute user lainnya ...
});

// Rute profil (bawaan Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// File rute autentikasi
require __DIR__.'/auth.php';