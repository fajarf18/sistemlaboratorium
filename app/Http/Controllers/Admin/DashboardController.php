<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang; // 1. Import model Barang
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin.
     */
    public function index(): View
    {
        // 2. Ambil 5 data barang terbaru dari database
        $barangs = Barang::latest()->take(5)->get();

        // 3. Kirim data ke view
        return view('admin.dashboard', [
            'barangs' => $barangs
        ]);
    }
}
