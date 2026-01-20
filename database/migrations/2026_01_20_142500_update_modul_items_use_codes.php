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
        // 1. Add new columns
        Schema::table('modul_items', function (Blueprint $table) {
            $table->string('kode_modul')->nullable()->after('id');
            $table->string('kode_barang')->nullable()->after('kode_modul');
        });

        // 2. Populate new columns from existing data
        $items = DB::table('modul_items')->get();
        foreach ($items as $item) {
            $modul = DB::table('moduls')->find($item->modul_id);
            $barang = DB::table('barangs')->find($item->barang_id);
            
            if ($modul && $barang) {
                DB::table('modul_items')
                    ->where('id', $item->id)
                    ->update([
                        'kode_modul' => $modul->kode_modul,
                        'kode_barang' => $barang->kode_barang
                    ]);
            }
        }

        // 3. Make columns required and add foreign keys
        Schema::table('modul_items', function (Blueprint $table) {
            // Modify columns to be non-nullable if they were nullable
            // (Note: In some DBs we might need to be careful, but here we assume migration runs on consistent state or empty state)
            // But we can't easily change nullable to not null without data. 
            // Since we populated, it should be fine. But let's check if we want to enforce FK first.
            
            // Drop old Foreign Keys first to avoid issues if we were to modify columns that are keys
            $table->dropForeign(['modul_id']);
            $table->dropForeign(['barang_id']);
        });
        
        // Split Schema operations provided strict mode issues
        Schema::table('modul_items', function (Blueprint $table) {
             $table->string('kode_modul')->nullable(false)->change();
             $table->string('kode_barang')->nullable(false)->change();
             
             // Add new FKs
             $table->foreign('kode_modul')->references('kode_modul')->on('moduls')->onDelete('cascade')->onUpdate('cascade');
             $table->foreign('kode_barang')->references('kode_barang')->on('barangs')->onDelete('cascade')->onUpdate('cascade');
             
             // Drop old columns
             $table->dropColumn(['modul_id', 'barang_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modul_items', function (Blueprint $table) {
            $table->foreignId('modul_id')->nullable()->after('id')->constrained('moduls')->onDelete('cascade');
            $table->foreignId('barang_id')->nullable()->after('modul_id')->constrained('barangs')->onDelete('cascade');
        });

        $items = DB::table('modul_items')->get();
        foreach ($items as $item) {
            $modul = DB::table('moduls')->where('kode_modul', $item->kode_modul)->first();
            $barang = DB::table('barangs')->where('kode_barang', $item->kode_barang)->first();
            
            if ($modul && $barang) {
                DB::table('modul_items')
                    ->where('id', $item->id)
                    ->update([
                        'modul_id' => $modul->id,
                        'barang_id' => $barang->id
                    ]);
            }
        }

        Schema::table('modul_items', function (Blueprint $table) {
             $table->dropForeign(['kode_modul']);
             $table->dropForeign(['kode_barang']);
             $table->dropColumn(['kode_modul', 'kode_barang']);
             
             // Make modul_id and barang_id non-nullable again? 
             // Ideally yes, but let's leave it simple for down method
        });
    }
};
