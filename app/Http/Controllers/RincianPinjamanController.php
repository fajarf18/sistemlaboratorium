<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RincianPinjamanController extends Controller
{
    public function index()
    {
        $peminjamans = Peminjaman::with('detailPeminjamans.barang', 'dosenPengampu')
            ->where('user_id', Auth::id())
            ->where('status', '!=', 'Dikembalikan')
            ->latest()
            ->get();

        // Cek apakah ada setidaknya satu peminjaman dengan status 'Dipinjam'
        $canReturn = $peminjamans->contains('status', 'Dipinjam');

        return view('user.rincian-pinjaman', [
            'peminjamans' => $peminjamans,
            'canReturn' => $canReturn, // Kirim variabel ini ke view
        ]);
    }
}