<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        // Ambil semua peminjaman yang masih aktif
        $activePeminjamans = Peminjaman::with('detailPeminjamans.barang')
            ->where('user_id', Auth::id())
            ->whereIn('status', ['Dipinjam', 'Menunggu Konfirmasi'])
            ->get();

        // Cek apakah ada yang statusnya 'Menunggu Konfirmasi'
        $hasPendingConfirmation = $activePeminjamans->contains('status', 'Menunggu Konfirmasi');

        // Cari tanggal wajib kembali terdekat HANYA dari yang statusnya 'Dipinjam'
        $nearestDueDate = null;
        if (!$hasPendingConfirmation) {
            $nearestDueDate = $activePeminjamans
                ->where('status', 'Dipinjam')
                ->min('tanggal_wajib_kembali');
        }

        return view('user.dashboard', [
            'activePeminjamans' => $activePeminjamans,
            'hasPendingConfirmation' => $hasPendingConfirmation,
            'nearestDueDate' => $nearestDueDate,
        ]);
    }
}