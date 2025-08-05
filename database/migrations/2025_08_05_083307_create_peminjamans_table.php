<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('peminjamans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained();
        $table->date('tanggal_pinjam');
        $table->date('tanggal_wajib_kembali');
        $table->date('tanggal_kembali')->nullable();
        
        // --- PERUBAHAN DI SINI ---
        // Mengubah dari string menjadi enum dengan daftar status yang diizinkan
        $table->enum('status', [
            'Menunggu Konfirmasi',
            'Dipinjam',
            'Dikembalikan',
            'Tunggu Konfirmasi Admin' // Nama baru untuk pengembalian
        ])->default('Menunggu Konfirmasi');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};
