<?php
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\PinjamBarangController; 
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\RincianPinjamanController;
use App\Http\Controllers\KembalikanBarangController;
use App\Http\Controllers\HistoryPeminjamanController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\KonfirmasiController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\ForgotPasswordController;

Route::middleware('guest')->group(function () {
    // Rute untuk Lupa Password
    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.email.send');
    
    Route::get('verify-password-otp', [ForgotPasswordController::class, 'showOtpForm'])->name('password.otp.form');
    Route::post('verify-password-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
    
    Route::get('reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update.new');

    Route::get('verify-otp', [OtpController::class, 'showOtpForm'])->name('otp.form');
    Route::post('verify-otp', [OtpController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('resend-otp', [OtpController::class, 'resendOtp'])->name('otp.resend'); 
    // Rute untuk tamu (landing page)
});
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

// Rute untuk Admin
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Rute untuk dashboard admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rute kustom untuk download harus didefinisikan SEBELUM resource controller
    Route::get('barang/download', [BarangController::class, 'download'])->name('barang.download');
    
    // Resource controller untuk menangani semua aksi CRUD standar.
    // Kita batasi hanya untuk metode yang ada di controller Anda untuk menghindari error.
    Route::resource('barang', BarangController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);
    //Pengaturan pengguna
    Route::get('users/download', [UserController::class, 'download'])->name('users.download');
    Route::resource('users', UserController::class)->only(['index', 'update', 'destroy']);
    Route::resource('users', UserController::class)->only(['index']);
    Route::resource('users', UserController::class)->only(['index', 'update']);
    Route::resource('users', UserController::class)->only(['index', 'update', 'destroy']);
Route::get('/konfirmasi', [KonfirmasiController::class, 'index'])->name('konfirmasi.index');
    Route::get('/konfirmasi/{id}', [KonfirmasiController::class, 'show'])->name('konfirmasi.show');
    Route::post('/konfirmasi/terima-peminjaman/{id}', [KonfirmasiController::class, 'terimaPeminjaman'])->name('konfirmasi.terimaPeminjaman');
    Route::post('/konfirmasi/tolak-peminjaman/{id}', [KonfirmasiController::class, 'tolakPeminjaman'])->name('konfirmasi.tolakPeminjaman');
    Route::post('/konfirmasi/terima-pengembalian/{id}', [KonfirmasiController::class, 'terimaPengembalian'])->name('konfirmasi.terimaPengembalian');
    Route::post('/konfirmasi/tolak-pengembalian/{id}', [KonfirmasiController::class, 'tolakPengembalian'])->name('konfirmasi.tolakPengembalian');
    Route::get('history/download', [HistoryController::class, 'download'])->name('history.download');
    Route::resource('history', HistoryController::class)->only(['index', 'show', 'destroy']);
    Route::get('/status-peminjam', [App\Http\Controllers\Admin\StatusPeminjamController::class, 'index'])->name('status.index');
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
        Route::get('/history-peminjaman/detail/{id}', [HistoryPeminjamanController::class, 'show'])->name('history.show');
    Route::get('verify-otp', [OtpController::class, 'showOtpForm'])->name('otp.form');
Route::post('verify-otp', [OtpController::class, 'verifyOtp'])->name('otp.verify');
    // ... rute user lainnya ...
});

// Rute profil (bawaan Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Rute Baru Untuk Halaman Barang
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
});
// File rute autentikasi
require __DIR__.'/auth.php';