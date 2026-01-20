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
        Schema::table('kelas_praktikums', function (Blueprint $table) {
            // Hapus foreign key dan kolom dosen_pengampu_id
            $table->dropForeign(['dosen_pengampu_id']);
            $table->dropColumn('dosen_pengampu_id');
            
            // Tambahkan kolom mata_kuliah
            $table->string('mata_kuliah')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas_praktikums', function (Blueprint $table) {
            // Kembalikan kolom dosen_pengampu_id
            $table->foreignId('dosen_pengampu_id')->after('id')->constrained('dosen_pengampus')->onDelete('cascade');
            
            // Hapus kolom mata_kuliah
            $table->dropColumn('mata_kuliah');
        });
    }
};
