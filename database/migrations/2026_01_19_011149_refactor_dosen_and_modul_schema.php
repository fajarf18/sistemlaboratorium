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
        // 1. Modul ownership (User 'dosen')
        Schema::table('moduls', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
        });

        // 2. Link Class to Modul
        Schema::table('kelas_praktikums', function (Blueprint $table) {
            $table->foreignId('modul_id')->nullable()->constrained('moduls')->nullOnDelete();
        });

        // 3. Update Peminjaman and Keranjang relationships (Point to Users instead of DosenPengampus)
        // Note: We need to handle data migration seamlessly if preserving old data is required, 
        // but given the request to "hilangkan tabel dosen_pengampus", we will switch the schema.
        // Assuming current data can be truncated or is fresh in dev environment.
        
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropForeign(['dosen_pengampu_id']);
            $table->dropColumn('dosen_pengampu_id');
            $table->foreignId('dosen_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
        });

        Schema::table('keranjangs', function (Blueprint $table) {
            $table->dropForeign(['dosen_pengampu_id']);
            $table->dropColumn('dosen_pengampu_id');
            $table->foreignId('dosen_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
        });

        // 4. Drop old table
        Schema::dropIfExists('dosen_pengampus');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-create old table
        Schema::create('dosen_pengampus', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nip')->unique();
            $table->string('email')->unique();
            $table->string('no_telp')->nullable();
            $table->string('mata_kuliah')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('keranjangs', function (Blueprint $table) {
            $table->dropForeign(['dosen_id']);
            $table->dropColumn('dosen_id');
            $table->foreignId('dosen_pengampu_id')->nullable()->constrained('dosen_pengampus');
        });

        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropForeign(['dosen_id']);
            $table->dropColumn('dosen_id');
            $table->foreignId('dosen_pengampu_id')->nullable()->constrained('dosen_pengampus');
        });

        Schema::table('kelas_praktikums', function (Blueprint $table) {
            $table->dropForeign(['modul_id']);
            $table->dropColumn('modul_id');
        });

        Schema::table('moduls', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
