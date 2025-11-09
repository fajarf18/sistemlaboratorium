<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE peminjaman_units MODIFY COLUMN status_pengembalian ENUM('belum', 'dikembalikan', 'rusak_ringan', 'rusak_berat') NOT NULL DEFAULT 'belum'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE peminjaman_units MODIFY COLUMN status_pengembalian ENUM('belum', 'dikembalikan', 'rusak', 'hilang') NOT NULL DEFAULT 'belum'");
    }
};

