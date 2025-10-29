<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\BarangUnit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BarangUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua barang
        $barangs = Barang::all();

        foreach ($barangs as $barang) {
            // Generate unit codes berdasarkan stok yang ada
            for ($i = 1; $i <= $barang->stok; $i++) {
                // Format kode: Inisial Barang + ID Barang + Nomor Unit
                // Contoh: GU-1-001 (Gelas Ukur, ID 1, Unit 001)
                $prefix = strtoupper(substr($barang->nama_barang, 0, 2));
                $unitCode = sprintf('%s-%d-%03d', $prefix, $barang->id, $i);

                BarangUnit::create([
                    'barang_id' => $barang->id,
                    'unit_code' => $unitCode,
                    'status' => 'baik', // Default semua baik
                    'keterangan' => null,
                ]);
            }

            $this->command->info("Generated {$barang->stok} units for {$barang->nama_barang}");
        }

        $this->command->info('Barang units seeded successfully!');
    }
}
