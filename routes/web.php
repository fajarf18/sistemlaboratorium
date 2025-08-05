<?php
use App\Http\Controllers\PinjamBarangController; 
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\RincianPinjamanController;
use App\Http\Controllers\KembalikanBarangController;
use App\Http\Controllers\HistoryPeminjamanController;
use App\Http\Controllers\UserDashboardController;

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
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
   Route::get('/pinjam-barang', [PinjamBarangController::class, 'index'])->name('pinjam.index');
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
    Route::post('/keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');
    Route::patch('/keranjang/{id}', [KeranjangController::class, 'update'])->name('keranjang.update');
    Route::delete('/keranjang/{id}', [KeranjangController::class, 'destroy'])->name('keranjang.destroy');
     Route::post('/checkout', function(Illuminate\Http\Request $request) {
        // Logika untuk memproses checkout akan ada di sini.
        // Anda bisa melihat item yang dipilih dengan:
        $selectedItems = json_decode($request->items);
        dd($selectedItems);
        return 'Proses checkout untuk item: ' . $request->items;
    })->name('checkout.process');
    Route::post('/checkout', [KeranjangController::class, 'checkout'])->name('checkout.process');
     Route::get('/rincian-pinjaman', [RincianPinjamanController::class, 'index'])->name('peminjaman.rincian');
     Route::get('/kembalikan-barang', [KembalikanBarangController::class, 'index'])->name('kembalikan.index');
         Route::get('/kembalikan-barang', [KembalikanBarangController::class, 'index'])->name('kembalikan.index');
    // RUTE BARU
    Route::post('/kembalikan-barang/konfirmasi', [KembalikanBarangController::class, 'konfirmasi'])->name('kembalikan.konfirmasi');
        Route::get('/history-peminjaman', [HistoryPeminjamanController::class, 'index'])->name('history.index');
    Route::get('/history-peminjaman/{id}', [HistoryPeminjamanController::class, 'show'])->name('history.show');
    
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