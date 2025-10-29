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
        Schema::create('peminjaman_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detail_peminjaman_id')->constrained('detail_peminjamans')->onDelete('cascade');
            $table->foreignId('barang_unit_id')->constrained('barang_units')->onDelete('cascade');
            $table->enum('status_pengembalian', ['belum', 'dikembalikan', 'rusak', 'hilang'])->default('belum');
            $table->text('keterangan_kondisi')->nullable(); // Keterangan jika rusak/hilang
            $table->string('foto_kondisi')->nullable(); // Foto jika rusak/hilang
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman_units');
    }
};
