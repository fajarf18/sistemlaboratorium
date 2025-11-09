<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Barang::query()->delete();

        $categories = [
            ['prefix' => 'TR', 'base_name' => 'Tabung Reaksi Borosilicate', 'tipe' => 'Tidak Habis Pakai', 'deskripsi' => 'Tabung reaksi kaca tahan panas untuk percobaan biokimia.', 'jumlah' => 10, 'stok_base' => 24, 'stok_increment' => 2],
            ['prefix' => 'MK', 'base_name' => 'Mikroskop Binokuler LED', 'tipe' => 'Tidak Habis Pakai', 'deskripsi' => 'Mikroskop dengan sistem pencahayaan LED dan pembesaran 40x-1000x.', 'jumlah' => 10, 'stok_base' => 10, 'stok_increment' => 1],
            ['prefix' => 'PP', 'base_name' => 'Pipet Mikro Adjustable', 'tipe' => 'Tidak Habis Pakai', 'deskripsi' => 'Pipet mikro adjustable untuk rentang volume 20-200 µL.', 'jumlah' => 10, 'stok_base' => 18, 'stok_increment' => 1],
            ['prefix' => 'GL', 'base_name' => 'Gelas Ukur Polipropilena', 'tipe' => 'Tidak Habis Pakai', 'deskripsi' => 'Gelas ukur plastik transparan dengan skala anti luntur.', 'jumlah' => 10, 'stok_base' => 30, 'stok_increment' => 3],
            ['prefix' => 'LB', 'base_name' => 'Labu Ukur Amber', 'tipe' => 'Tidak Habis Pakai', 'deskripsi' => 'Labu ukur kaca amber untuk larutan sensitif cahaya.', 'jumlah' => 10, 'stok_base' => 20, 'stok_increment' => 2],
            ['prefix' => 'BR', 'base_name' => 'Buret Kaca Stopcock PTFE', 'tipe' => 'Tidak Habis Pakai', 'deskripsi' => 'Buret dengan stopcock PTFE untuk titrasi presisi.', 'jumlah' => 10, 'stok_base' => 16, 'stok_increment' => 1],
            ['prefix' => 'PH', 'base_name' => 'pH Meter Portable', 'tipe' => 'Tidak Habis Pakai', 'deskripsi' => 'Alat ukur pH portable dengan kalibrasi otomatis.', 'jumlah' => 10, 'stok_base' => 8, 'stok_increment' => 1],
            ['prefix' => 'SL', 'base_name' => 'Slide Preparat Polos', 'tipe' => 'Habis Pakai', 'deskripsi' => 'Slide kaca polos untuk preparat histologi.', 'jumlah' => 10, 'stok_base' => 100, 'stok_increment' => 5],
            ['prefix' => 'AP', 'base_name' => 'APD Jas Laboratorium', 'tipe' => 'Tidak Habis Pakai', 'deskripsi' => 'Jas laboratorium katun dengan sirkulasi udara baik.', 'jumlah' => 10, 'stok_base' => 40, 'stok_increment' => 2],
            ['prefix' => 'RG', 'base_name' => 'Rak Tabung Reaksi Stainless', 'tipe' => 'Tidak Habis Pakai', 'deskripsi' => 'Rak tabung reaksi stainless steel kapasitas 24 slot.', 'jumlah' => 10, 'stok_base' => 22, 'stok_increment' => 1],
        ];

        foreach ($categories as $category) {
            for ($i = 1; $i <= $category['jumlah']; $i++) {
                $kode = sprintf('%s-%03d', $category['prefix'], $i);
                $nama = sprintf('%s Seri %s', $category['base_name'], str_pad((string) $i, 2, '0', STR_PAD_LEFT));
                $stok = $category['stok_base'] + (($i - 1) % 4) * $category['stok_increment'];

                Barang::create([
                    'kode_barang' => $kode,
                    'nama_barang' => $nama,
                    'tipe' => $category['tipe'],
                    'stok' => $stok,
                    'deskripsi' => $category['deskripsi'],
                    'gambar' => 'https://placehold.co/200x200/1e293b/fff?text=' . urlencode($kode),
                ]);
            }
        }
    }
}

