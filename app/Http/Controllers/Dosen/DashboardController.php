<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\KelasPraktikum;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dosen.
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Statistik kelas praktikum
        $totalKelas = KelasPraktikum::where('created_by', $user->id)->count();
        $kelasAktif = KelasPraktikum::where('created_by', $user->id)
            ->where('is_active', true)
            ->count();
        
        // Statistik peminjaman yang menunggu konfirmasi dosen
        // Statistik peminjaman yang menunggu konfirmasi dosen
        $peminjamanMenunggu = Peminjaman::where('dosen_id', $user->id)
            ->where('status', 'Menunggu Konfirmasi')
            ->whereNull('dosen_konfirmasi_at')
            ->count();
        
        // Kelas terbaru
        $kelasTerbaru = KelasPraktikum::where('created_by', $user->id)
            ->with('creator')
            ->latest()
            ->limit(5)
            ->get();
        
        return view('dosen.dashboard', compact(
            'totalKelas',
            'kelasAktif',
            'peminjamanMenunggu',
            'kelasTerbaru'
        ));
    }
}
