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
    Schema::create('history_peminjamans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('peminjaman_id')->references('id')->on('peminjamans');
        $table->foreignId('user_id')->constrained();
        $table->date('tanggal_kembali');
        $table->string('status_pengembalian'); // Aman / Hilang
        $table->text('deskripsi_kehilangan')->nullable();
        $table->string('gambar_bukti')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_peminjamans');
    }
};
