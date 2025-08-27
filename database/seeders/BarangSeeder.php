<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Barang; // Pastikan untuk mengimpor model Barang

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama untuk menghindari duplikat saat seeder dijalankan ulang
        Barang::query()->delete();

        Barang::create([
            'kode_barang' => 'A01',
            'nama_barang' => 'Tabung Reaksi Kaca',
            'tipe' => 'Alat Gelas',
            'stok' => 50,
            'gambar' => 'https://placehold.co/100x100/e2e8f0/334155?text=Alat'
        ]);

        Barang::create([
            'kode_barang' => 'A02',
            'nama_barang' => 'Mikroskop Binokuler',
            'tipe' => 'Alat Optik',
            'stok' => 15,
            'gambar' => 'https://placehold.co/100x100/e2e8f0/334155?text=Alat'
        ]);

        Barang::create([
            'kode_barang' => 'A03',
            'nama_barang' => 'Sarung Tangan Latex (Box)',
            'tipe' => 'Habis Pakai',
            'stok' => 30,
            'gambar' => 'https://placehold.co/100x100/e2e8f0/334155?text=Alat'
        ]);

        Barang::create([
            'kode_barang' => 'A04',
            'nama_barang' => 'Pipet Tetes Kaca',
            'tipe' => 'Alat Gelas',
            'stok' => 100,
            'gambar' => 'https://placehold.co/100x100/e2e8f0/334155?text=Alat'
        ]);

        Barang::create([
            'kode_barang' => 'A05',
            'nama_barang' => 'Larutan Etanol 70%',
            'tipe' => 'Bahan Kimia',
            'stok' => 25,
            'gambar' => 'https://placehold.co/100x100/e2e8f0/334155?text=Alat'
        ]);

        Barang::create([
            'kode_barang' => 'A06',
            'nama_barang' => 'Jas Laboratorium',
            'tipe' => 'APD',
            'stok' => 40,
            'gambar' => 'https://placehold.co/100x100/e2e8f0/334155?text=Alat'
        ]);
        
        // Anda bisa menambahkan lebih banyak data di sini
    }
}
