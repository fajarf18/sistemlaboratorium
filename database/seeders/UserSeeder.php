<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Pastikan model User diimpor
use Illuminate\Support\Facades\Hash; // Impor Hash untuk enkripsi password

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama untuk menghindari duplikat
        User::query()->delete();

        // 1. Buat Akun Admin
        User::create([
            'nama' => 'Admin Lab',
            'nim' => '123', // NIM untuk admin bisa berupa teks unik
            'email' => 'admin@stikes.ac.id',
            'prodi' => 'Administrasi',
            'semester' => 'N/A',
            'password' => Hash::make('admin123'), // Ganti 'password' dengan password yang aman
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // 2. Buat Akun User (Mahasiswa)
        User::create([
            'nama' => 'Budi Sanjaya',
            'nim' => '1111',
            'email' => 'budi.sanjaya@student.stikes.ac.id',
            'prodi' => 'Keperawatan',
            'semester' => '3',
            'password' => Hash::make('12345678'), // Ganti 'password' dengan password yang aman
            'role' => 'user',
            'email_verified_at' => now(),
        ]);
    }
}