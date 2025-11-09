<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\BarangUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BarangUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BarangUnit::query()->delete();

        $barangs = Barang::all();

        foreach ($barangs as $barang) {
            $prefix = strtoupper(Str::slug($barang->kode_barang, ''));

            for ($i = 1; $i <= $barang->stok; $i++) {
                $status = 'baik';
                $keterangan = null;

                if ($i === 1 && $barang->id % 9 === 0) {
                    $status = 'rusak_ringan';
                    $keterangan = "Unit awal {$barang->nama_barang} perlu perbaikan ringan.";
                } elseif ($i === 2 && $barang->id % 13 === 0) {
                    $status = 'rusak_berat';
                    $keterangan = "{$barang->nama_barang} seri {$barang->kode_barang} retak berat.";
                } elseif ($i <= 2 && $barang->id % 7 === 0) {
                    $status = 'dipinjam';
                    $keterangan = 'Sedang dipakai untuk praktikum aktif.';
                }

                BarangUnit::create([
                    'barang_id' => $barang->id,
                    'unit_code' => sprintf('%s-%03d', $prefix, $i),
                    'status' => $status,
                    'keterangan' => $keterangan,
                ]);
            }
        }
    }
}
