<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon; // Import Carbon
use App\Exports\HistoryPeminjamanExport;
use Maatwebsite\Excel\Facades\Excel;

class HistoryController extends Controller
{
    /**
     * Menampilkan halaman riwayat peminjaman yang sudah selesai.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = Peminjaman::with([
            'user',
            'dosenPengampu',
            'history',
            'detailPeminjaman.barang',
            'detailPeminjaman.peminjamanUnits.barangUnit'
        ])
            ->where('status', 'Dikembalikan');

        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nim', 'like', '%' . $search . '%');
            });
        }

        $histories = $query->latest('tanggal_kembali')->paginate(10)->withQueryString();

        return view('admin.history.index', compact('histories', 'search'));
    }

    /**
     * Menampilkan detail peminjaman untuk modal preview.
     */
    public function show($id)
    {
        $history = Peminjaman::with([
            'user',
            'dosenPengampu',
            'detailPeminjaman.barang',
            'detailPeminjaman.peminjamanUnits.barangUnit',
            'history'
        ])
            ->findOrFail($id);

        return response()->json($history);
    }

    /**
     * Menghapus data riwayat peminjaman.
     */
    public function destroy($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        $peminjaman->history()->delete();
        $peminjaman->detailPeminjamans()->delete();
        $peminjaman->delete();

        return redirect()->route('admin.history.index')->with('success', 'Riwayat peminjaman berhasil dihapus.');
    }

    /**
     * Men-download data riwayat sebagai file Excel.
     */
    public function download(Request $request)
    {
        $search = $request->input('search');
        $fileName = 'riwayat-peminjaman-' . date('Y-m-d-His') . '.xlsx';

        return Excel::download(new HistoryPeminjamanExport($search), $fileName);
    }
}