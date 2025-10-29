<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE barang_units MODIFY COLUMN status ENUM('baik', 'rusak', 'hilang') NOT NULL DEFAULT 'baik'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE barang_units MODIFY COLUMN status ENUM('baik', 'rusak') NOT NULL DEFAULT 'baik'");
    }
};
