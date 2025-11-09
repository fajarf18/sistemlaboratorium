<?php

namespace Database\Seeders;

use App\Models\DosenPengampu;
use Illuminate\Database\Seeder;

class DosenPengampuSeeder extends Seeder
{
    public function run(): void
    {
        DosenPengampu::query()->delete();

        $dosens = [
            [
                'nama' => 'Dr. Kirana Lestari, M.Si',
                'nip' => '19760101',
                'email' => 'kirana.lestari@stikes.ac.id',
                'no_telp' => '081299887766',
                'mata_kuliah' => 'Praktikum Biokimia',
                'is_active' => true,
            ],
            [
                'nama' => 'Drs. Bambang Prasetyo',
                'nip' => '19781212',
                'email' => 'bambang.prasetyo@stikes.ac.id',
                'no_telp' => '082177665544',
                'mata_kuliah' => 'Praktikum Histologi',
                'is_active' => true,
            ],
            [
                'nama' => 'Dr. Nia Wulandari, Apt',
                'nip' => '19820315',
                'email' => 'nia.wulandari@stikes.ac.id',
                'no_telp' => '085155443322',
                'mata_kuliah' => 'Praktikum Farmasetika',
                'is_active' => true,
            ],
        ];

        foreach ($dosens as $dosen) {
            DosenPengampu::create($dosen);
        }
    }
}

