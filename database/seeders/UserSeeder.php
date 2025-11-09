<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->delete();

        $now = now();

        User::create([
            'nama' => 'Admin Lab',
            'nim' => 'ADM-001',
            'email' => 'admin@stikes.ac.id',
            'prodi' => 'Administrasi Laboratorium',
            'semester' => 'N/A',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => $now,
        ]);

        $students = [
            ['Fajar Fathurrozak', '2211001', 'fajar.fathurrozak@student.stikes.ac.id', '089600011223', 'S1 Keperawatan', '4'],
            ['Nadia Shafira', '2211002', 'nadia.shafira@student.stikes.ac.id', '082178990011', 'D3 Kebidanan', '3'],
            ['Rafi Rahman', '2211003', 'rafi.rahman@student.stikes.ac.id', '085155664433', 'S1 Farmasi', '5'],
            ['Syifa Maharani', '2211004', 'syifa.maharani@student.stikes.ac.id', '081288990022', 'D4 Analis Kesehatan', '2'],
            ['Yoga Ardana', '2211005', 'yoga.ardana@student.stikes.ac.id', '087733221100', 'S1 Gizi', '6'],
            ['Azka Nurhaliza', '2211006', 'azka.nurhaliza@student.stikes.ac.id', '082144567890', 'S1 Keperawatan', '2'],
            ['Bagas Saputra', '2211007', 'bagas.saputra@student.stikes.ac.id', '085300112233', 'D3 Teknologi Laboratorium', '5'],
            ['Cici Ramadhani', '2211008', 'cici.ramadhani@student.stikes.ac.id', '081377665532', 'S1 Kebidanan', '7'],
            ['Dimas Januar', '2211009', 'dimas.januar@student.stikes.ac.id', '089500776655', 'S1 Farmasi', '8'],
            ['Eka Yuliana', '2211010', 'eka.yuliana@student.stikes.ac.id', '082244557788', 'D4 Analis Kesehatan', '3'],
            ['Fitria Az Zahra', '2211011', 'fitria.zahra@student.stikes.ac.id', '085266443322', 'S1 Administrasi Rumah Sakit', '5'],
            ['Gilang Perdana', '2211012', 'gilang.perdana@student.stikes.ac.id', '081333998877', 'S1 Keperawatan', '7'],
            ['Hafidz Rahadian', '2211013', 'hafidz.rahadian@student.stikes.ac.id', '082233445566', 'S1 Gizi', '6'],
            ['Intan Maharani', '2211014', 'intan.maharani@student.stikes.ac.id', '083811004455', 'S1 Kebidanan', '4'],
            ['Jasmine Prameswari', '2211015', 'jasmine.prameswari@student.stikes.ac.id', '081355577799', 'S1 Farmasi', '8'],
            ['Kezia Natalia', '2211016', 'kezia.natalia@student.stikes.ac.id', '085877665500', 'D3 Kebidanan', '5'],
            ['Luthfi Ramadhan', '2211017', 'luthfi.ramadhan@student.stikes.ac.id', '081244667788', 'S1 Fisioterapi', '6'],
            ['Maira Yunita', '2211018', 'maira.yunita@student.stikes.ac.id', '089933221144', 'S1 Administrasi Rumah Sakit', '4'],
            ['Naufal Hilmi', '2211019', 'naufal.hilmi@student.stikes.ac.id', '087711220033', 'S1 Keperawatan', '3'],
            ['Olivia Permata', '2211020', 'olivia.permata@student.stikes.ac.id', '081277665544', 'S1 Gizi', '2'],
        ];

        foreach ($students as [$nama, $nim, $email, $nomorWa, $prodi, $semester]) {
            User::create([
                'nama' => $nama,
                'nim' => $nim,
                'email' => $email,
                'nomor_wa' => $nomorWa,
                'prodi' => $prodi,
                'semester' => $semester,
                'password' => Hash::make('password123'),
                'role' => 'user',
                'email_verified_at' => $now,
            ]);
        }
    }
}

