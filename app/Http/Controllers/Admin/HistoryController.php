<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon; // Import Carbon

class HistoryController extends Controller
{
    /**
     * Menampilkan halaman riwayat peminjaman yang sudah selesai.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = Peminjaman::with(['user', 'history', 'detailPeminjamans.barang'])
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
        $history = Peminjaman::with(['user', 'detailPeminjamans.barang', 'history'])
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
     * Men-download data riwayat sebagai file CSV.
     */
    public function download(Request $request)
    {
        $search = $request->input('search');
        // Eager load semua relasi yang dibutuhkan
        $query = Peminjaman::with(['user', 'history', 'detailPeminjamans.barang'])
                           ->where('status', 'Dikembalikan');

        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nim', 'like', '%' . $search . '%');
            });
        }

        $histories = $query->get();
        $fileName = "riwayat-peminjaman-" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Tambahkan kolom baru di header
        $columns = ['ID Peminjaman', 'NIM', 'Nama Peminjam', 'Tanggal Pinjam', 'Tanggal Kembali', 'Status Pengembalian', 'Barang Dipinjam', 'Barang Hilang', 'Deskripsi Kehilangan'];

        $callback = function() use($histories, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($histories as $peminjaman) {
                // Siapkan data barang dipinjam dan hilang
                $barangDipinjam = [];
                $barangHilang = [];
                foreach ($peminjaman->detailPeminjamans as $detail) {
                    $totalPinjam = $detail->jumlah + $detail->jumlah_hilang;
                    $barangDipinjam[] = "{$detail->barang->nama_barang} ({$totalPinjam})";

                    if ($detail->jumlah_hilang > 0) {
                        $barangHilang[] = "{$detail->barang->nama_barang} ({$detail->jumlah_hilang})";
                    }
                }

                fputcsv($file, [
                    $peminjaman->id,
                    $peminjaman->user->nim,
                    $peminjaman->user->nama,
                    Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y'), // Format tanggal
                    Carbon::parse($peminjaman->tanggal_kembali)->format('d/m/Y'), // Format tanggal
                    optional($peminjaman->history)->status_pengembalian ?? 'N/A',
                    implode(', ', $barangDipinjam), // Gabungkan array menjadi string
                    count($barangHilang) > 0 ? implode(', ', $barangHilang) : 'Tidak ada', // Tampilkan jika ada
                    optional($peminjaman->history)->deskripsi_kehilangan ?? 'Tidak ada',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}