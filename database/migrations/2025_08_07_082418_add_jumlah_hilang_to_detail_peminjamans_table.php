<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detail_peminjamans', function (Blueprint $table) {
            // Menambahkan kolom jumlah_rusak setelah kolom jumlah
            $table->integer('jumlah_rusak')->default(0)->after('jumlah');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detail_peminjamans', function (Blueprint $table) {
            $table->dropColumn('jumlah_rusak');
        });
    }
};