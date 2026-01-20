<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryPeminjamanController extends Controller
{
    /**
     * Menampilkan halaman utama history peminjaman.
     */
    public function index()
    {
        // Ambil semua data peminjaman milik user yang sedang login dengan relasi history
        $historyPeminjamans = Peminjaman::with('history', 'dosen', 'detailPeminjamans.peminjamanUnits')
            ->where('user_id', Auth::id())
            ->latest('tanggal_pinjam')
            ->get();

        // Mengirim data ke view 'user.history-peminjaman'.
        return view('user.history-peminjaman', compact('historyPeminjamans'));
    }

    /**
     * Mengambil detail spesifik dari sebuah peminjaman untuk ditampilkan di modal.
     * Method ini akan dipanggil oleh fetch() dari Alpine.js.
     *
     * @param int $id ID dari tabel peminjamans
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Cari data peminjaman berdasarkan ID dan pastikan milik user yang login.
        // Load semua relasi yang dibutuhkan termasuk unit-unit individual
        $peminjaman = Peminjaman::with([
            'dosen',
            'detailPeminjaman.barang',
            'detailPeminjaman.peminjamanUnits.barangUnit',
            'history'
        ])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Mengembalikan data lengkap sebagai JSON.
        // Alpine.js di sisi frontend akan menerima objek ini dan menggunakannya
        // untuk menampilkan detail, termasuk memfilter dan menampilkan barang yang hilang.
        return response()->json($peminjaman);
    }
}
