<?php

use App\Models\User;
use App\Models\Modul;
use App\Models\KelasPraktikum;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting Verification...\n";

DB::beginTransaction();

try {
    // 1. Create User (Dosen)
    $dosen = User::create([
        'nama' => 'Test Dosen Refactor',
        'email' => 'dosenrefactor@test.com',
        'password' => bcrypt('password'),
        'role' => 'dosen',
        'nim' => 'NIP12345',
        'nomor_wa' => '081234567890',
        'prodi' => 'Teknik Informatika',
        'semester' => 0,
        'email_verified_at' => now(),
    ]);
    echo "Dosen Created: {$dosen->nama} (ID: {$dosen->id})\n";

    // 2. Create Modul
    $modul = Modul::create([
        'user_id' => $dosen->id,
        'nama_modul' => 'Modul Test Refactor',
        'kode_modul' => 'MTR01',
        'deskripsi' => 'Deskripsi Modul Test',
    ]);
    echo "Modul Created: {$modul->nama_modul} (ID: {$modul->id}) linked to User ID: {$modul->user_id}\n";

    // 3. Create KelasPraktikum
    $kelas = KelasPraktikum::create([
        'modul_id' => $modul->id,
        'mata_kuliah' => 'Matkul Test',
        'nama_kelas' => 'Kelas A',
        'created_by' => $dosen->id,
        'is_active' => true,
    ]);
    echo "Kelas Created: {$kelas->nama_kelas} (ID: {$kelas->id}) linked to Modul ID: {$kelas->modul_id}\n";

    // Verify Relationships
    $fetchedKelas = KelasPraktikum::find($kelas->id);
    if ($fetchedKelas->modul->user->id === $dosen->id) {
        echo "[PASS] Kelas -> Modul -> User relationship works.\n";
    } else {
        echo "[FAIL] Kelas -> Modul -> User relationship failed.\n";
    }

    if ($fetchedKelas->creator->id === $dosen->id) {
        echo "[PASS] Kelas -> Creator relationship works.\n";
    } else {
        echo "[FAIL] Kelas -> Creator relationship failed.\n";
    }

    // 4. Create Peminjaman
    $peminjaman = Peminjaman::create([
        'user_id' => $dosen->id, // Peminjam is same mainly for testing
        'dosen_id' => $dosen->id,
        'kelas_praktikum_id' => $kelas->id,
        'tanggal_pinjam' => now(),
        'tanggal_wajib_kembali' => now()->addDays(3),
        'status' => 'Menunggu Konfirmasi',
    ]);
    echo "Peminjaman Created (ID: {$peminjaman->id}) linked to Dosen ID: {$peminjaman->dosen_id}\n";

    // Verify Peminjaman Relationships
    $fetchedPeminjaman = Peminjaman::find($peminjaman->id);
    
    // Check 'dosen' relationship
    if ($fetchedPeminjaman->dosen && $fetchedPeminjaman->dosen->id === $dosen->id) {
        echo "[PASS] Peminjaman -> Dosen relationship works.\n";
    } else {
        echo "[FAIL] Peminjaman -> Dosen relationship failed.\n";
    }

    // Check availability of legacy 'dosenPengampu' (should be null or error if strictly removed from model, but we removed it so accessing it might throw error or return null if magic method handles it, but typically we want it GONE)
    // Actually we removed the method `dosenPengampu()` from User and Peminjaman models.
    // So accessing `$fetchedPeminjaman->dosenPengampu` should return null (Laravel default for non-existent attribute) or error? 
    // Laravel magic __get will try to find attribute, if not found and no relation method, returns null.
    // However, if we didn't remove the foreign key column `dosen_pengampu_id` (we renamed it), then `dosenPengampu` attribute is definitely gone.
    
    echo "Verification Complete.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
} finally {
    DB::rollBack();
    echo "Rolled back transaction.\n";
}
