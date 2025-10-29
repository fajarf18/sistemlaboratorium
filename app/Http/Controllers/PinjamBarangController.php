<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Peminjaman; // Pastikan ini diimpor
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Pastikan ini diimpor

class PinjamBarangController extends Controller
{
    public function index(Request $request)
    {
        // Cek apakah user punya pinjaman dengan status aktif
        $hasActiveLoan = Peminjaman::where('user_id', Auth::id())
            ->whereIn('status', [
                'Dipinjam', 
                'Tunggu Konfirmasi Admin' // <-- TAMBAHKAN STATUS INI
            ])
            ->exists();

        // Jika punya pinjaman aktif, langsung tampilkan view larangan
        if ($hasActiveLoan) {
            return view('user.larangan-pinjam');
        }

        // Jika tidak, lanjutkan logika seperti biasa
        $query = Barang::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        if ($request->has('filter') && $request->filter != 'semua') {
            $query->where('tipe', $request->filter);
        }

        $barangs = $query->paginate(10)->withQueryString();

        return view('user.pinjam-barang', [
            'barangs' => $barangs,
            'tipeBarang' => Barang::select('tipe')->distinct()->get()
        ]);
    }
}