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
        Schema::create('kelas_praktikums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_pengampu_id')->constrained('dosen_pengampus')->onDelete('cascade');
            $table->string('nama_kelas');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_praktikum')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas_praktikums');
    }
};
