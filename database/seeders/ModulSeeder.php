<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Modul;
use App\Models\ModulItem;
use Illuminate\Database\Seeder;

class ModulSeeder extends Seeder
{
    public function run(): void
    {
        ModulItem::query()->delete();
        Modul::query()->delete();

        $barangMap = Barang::pluck('id', 'kode_barang');

        $modules = [
            [
                'nama_modul' => 'Praktikum Biokimia Dasar',
                'kode_modul' => 'BIO-PR-01',
                'deskripsi' => 'Analisis karbohidrat, protein, dan lipid.',
                'items' => [
                    ['kode_barang' => 'TR-001', 'jumlah' => 12],
                    ['kode_barang' => 'PP-003', 'jumlah' => 6],
                    ['kode_barang' => 'GL-002', 'jumlah' => 6],
                ],
            ],
            [
                'nama_modul' => 'Praktikum Histologi',
                'kode_modul' => 'HIS-PR-02',
                'deskripsi' => 'Pengamatan jaringan menggunakan mikroskop.',
                'items' => [
                    ['kode_barang' => 'MK-001', 'jumlah' => 6],
                    ['kode_barang' => 'SL-004', 'jumlah' => 40],
                    ['kode_barang' => 'AP-002', 'jumlah' => 10],
                ],
            ],
            [
                'nama_modul' => 'Praktikum Mikrobiologi',
                'kode_modul' => 'MIK-PR-03',
                'deskripsi' => 'Kultur bakteri dan uji sensitivitas antibiotik.',
                'items' => [
                    ['kode_barang' => 'TR-004', 'jumlah' => 20],
                    ['kode_barang' => 'RG-003', 'jumlah' => 5],
                    ['kode_barang' => 'PP-006', 'jumlah' => 8],
                ],
            ],
            [
                'nama_modul' => 'Praktikum Kimia Analitik',
                'kode_modul' => 'KIM-PR-04',
                'deskripsi' => 'Titrasi asam-basa dan gravimetri.',
                'items' => [
                    ['kode_barang' => 'BR-002', 'jumlah' => 12],
                    ['kode_barang' => 'GL-006', 'jumlah' => 10],
                    ['kode_barang' => 'LB-003', 'jumlah' => 8],
                ],
            ],
            [
                'nama_modul' => 'Praktikum Farmasetika',
                'kode_modul' => 'FAR-PR-05',
                'deskripsi' => 'Formulasi sediaan cair dan semi padat.',
                'items' => [
                    ['kode_barang' => 'LB-005', 'jumlah' => 6],
                    ['kode_barang' => 'PP-008', 'jumlah' => 6],
                    ['kode_barang' => 'AP-004', 'jumlah' => 10],
                ],
            ],
            [
                'nama_modul' => 'Praktikum Gizi Klinik',
                'kode_modul' => 'GIZ-PR-06',
                'deskripsi' => 'Pengukuran kandungan gizi bahan pangan.',
                'items' => [
                    ['kode_barang' => 'GL-008', 'jumlah' => 8],
                    ['kode_barang' => 'PH-003', 'jumlah' => 4],
                    ['kode_barang' => 'TR-007', 'jumlah' => 12],
                ],
            ],
            [
                'nama_modul' => 'Praktikum Fisiologi',
                'kode_modul' => 'FIS-PR-07',
                'deskripsi' => 'Pengukuran parameter vital tubuh.',
                'items' => [
                    ['kode_barang' => 'MK-005', 'jumlah' => 4],
                    ['kode_barang' => 'PP-001', 'jumlah' => 6],
                    ['kode_barang' => 'PH-004', 'jumlah' => 4],
                ],
            ],
            [
                'nama_modul' => 'Praktikum Patologi Klinik',
                'kode_modul' => 'PAT-PR-08',
                'deskripsi' => 'Analisis spesimen darah dan urin.',
                'items' => [
                    ['kode_barang' => 'SL-006', 'jumlah' => 40],
                    ['kode_barang' => 'TR-009', 'jumlah' => 15],
                    ['kode_barang' => 'PP-010', 'jumlah' => 8],
                ],
            ],
            [
                'nama_modul' => 'Praktikum Sterilisasi Alat',
                'kode_modul' => 'STE-PR-09',
                'deskripsi' => 'Validasi proses sterilisasi laboratorium.',
                'items' => [
                    ['kode_barang' => 'AP-006', 'jumlah' => 10],
                    ['kode_barang' => 'RG-007', 'jumlah' => 6],
                    ['kode_barang' => 'TR-010', 'jumlah' => 10],
                ],
            ],
            [
                'nama_modul' => 'Praktikum Instrumentasi',
                'kode_modul' => 'INS-PR-10',
                'deskripsi' => 'Pengenalan alat instrumentasi modern.',
                'items' => [
                    ['kode_barang' => 'PH-007', 'jumlah' => 6],
                    ['kode_barang' => 'MK-009', 'jumlah' => 4],
                    ['kode_barang' => 'BR-008', 'jumlah' => 6],
                ],
            ],
        ];

        foreach ($modules as $module) {
            $modul = Modul::create([
                'nama_modul' => $module['nama_modul'],
                'deskripsi' => $module['deskripsi'],
                'kode_modul' => $module['kode_modul'],
                'is_active' => true,
            ]);

            foreach ($module['items'] as $item) {
                $barangId = $barangMap[$item['kode_barang']] ?? null;

                if (!$barangId) {
                    continue;
                }

                ModulItem::create([
                    'modul_id' => $modul->id,
                    'barang_id' => $barangId,
                    'jumlah' => $item['jumlah'],
                ]);
            }
        }
    }
}

