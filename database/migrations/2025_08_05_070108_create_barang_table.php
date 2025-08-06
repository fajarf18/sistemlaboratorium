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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
            $table->string('kode_barang')->unique(); // <-- KOLOM YANG HILANG SUDAH DITAMBAHKAN
            $table->string('tipe'); // Contoh: "Habis Pakai", "Tidak Habis Pakai"
            $table->integer('stok');
            $table->string('gambar')->nullable(); // Path ke gambar barang
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs'); // Diperbaiki dari 'barang' menjadi 'barangs'
    }
};
