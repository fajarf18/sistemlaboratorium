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
        Schema::table('detail_peminjamans', function (Blueprint $table) {
            // 1. Hapus foreign key yang lama
            $table->dropForeign(['barang_id']);

            // 2. Tambahkan lagi foreign key yang baru dengan aturan onDelete('cascade')
            $table->foreign('barang_id')
                  ->references('id')
                  ->on('barangs')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_peminjamans', function (Blueprint $table) {
            // Jika perlu rollback, kembalikan seperti semula
            $table->dropForeign(['barang_id']);

            $table->foreign('barang_id')
                  ->references('id')
                  ->on('barangs');
        });
    }
};