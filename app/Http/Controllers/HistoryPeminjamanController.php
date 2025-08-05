<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryPeminjamanController extends Controller
{
    public function index()
    {
        // Ambil semua peminjaman milik user, urutkan dari yang terbaru
        $historyPeminjamans = Peminjaman::where('user_id', Auth::id())
            ->latest('tanggal_pinjam')
            ->get();

        return view('user.history-peminjaman', compact('historyPeminjamans'));
    }

    public function show($id)
    {
        // Ambil detail peminjaman spesifik untuk ditampilkan di modal
        $peminjaman = Peminjaman::with('detailPeminjamans.barang')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Mengembalikan response JSON untuk diambil oleh Alpine.js
        return response()->json($peminjaman);
    }
}