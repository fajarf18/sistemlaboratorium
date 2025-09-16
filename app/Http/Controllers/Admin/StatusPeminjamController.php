<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatusPeminjamController extends Controller
{
    /**
     * Menampilkan halaman status peminjam yang aktif.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        // Mengambil peminjaman yang statusnya BUKAN 'Dikembalikan'
        $query = Peminjaman::with(['user', 'detailPeminjamans.barang'])
                           ->where('status', '!=', 'Dikembalikan');

        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nim', 'like', '%' . $search . '%');
            });
        }

        $activePeminjamans = $query->latest('tanggal_pinjam')->paginate(10)->withQueryString();

        return view('admin.status.index', compact('activePeminjamans', 'search'));
    }
}