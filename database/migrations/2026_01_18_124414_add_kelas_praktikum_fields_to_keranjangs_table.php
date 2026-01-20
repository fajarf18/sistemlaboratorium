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
        Schema::table('keranjangs', function (Blueprint $table) {
            $table->foreignId('kelas_praktikum_id')->nullable()->after('barang_id')->constrained('kelas_praktikums')->onDelete('cascade');
            $table->foreignId('dosen_pengampu_id')->nullable()->after('kelas_praktikum_id')->constrained('dosen_pengampus')->onDelete('set null');
            $table->boolean('dari_kelas')->default(false)->after('dosen_pengampu_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keranjangs', function (Blueprint $table) {
            $table->dropForeign(['kelas_praktikum_id']);
            $table->dropForeign(['dosen_pengampu_id']);
            $table->dropColumn(['kelas_praktikum_id', 'dosen_pengampu_id', 'dari_kelas']);
        });
    }
};
