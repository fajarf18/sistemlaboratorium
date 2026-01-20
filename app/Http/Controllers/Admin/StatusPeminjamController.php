<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\BarangUnit;
use App\Models\HistoryPeminjaman;
use App\Models\DetailPeminjaman;
use App\Models\PeminjamanUnit;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatusPeminjamController extends Controller
{
    /**
     * Menampilkan halaman status peminjam yang aktif.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        // Mengambil peminjaman yang statusnya BUKAN 'Dikembalikan'
        $query = Peminjaman::with([
            'user',
            'dosen',
            'detailPeminjamans.barang',
            'detailPeminjamans.peminjamanUnits.barangUnit'
        ])
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

    /**
     * Menyelesaikan peminjaman (status Dipinjam -> Dikembalikan)
     * Mengembalikan semua barang ke stok dengan status baik
     */
    public function selesaikan($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);

            // Validasi status harus Dipinjam
            if ($peminjaman->status !== 'Dipinjam') {
                return redirect()->back()->with('error', 'Peminjaman hanya bisa diselesaikan jika statusnya Dipinjam.');
            }

            DB::beginTransaction();

            // Update status peminjaman
            $peminjaman->tanggal_kembali = now();
            $peminjaman->status = 'Dikembalikan';
            $peminjaman->save();

            // Ambil semua detail peminjaman
            $detailPeminjamans = DetailPeminjaman::where('peminjaman_id', $peminjaman->id)->get();

            foreach ($detailPeminjamans as $detail) {
                // Ambil semua unit yang dipinjam
                $peminjamanUnits = PeminjamanUnit::where('detail_peminjaman_id', $detail->id)->get();

                $jumlahDikembalikan = 0;

                foreach ($peminjamanUnits as $peminjamanUnit) {
                    // Update status unit barang menjadi 'baik'
                    $barangUnit = BarangUnit::find($peminjamanUnit->barang_unit_id);
                    if ($barangUnit) {
                        $barangUnit->status = 'baik';
                        $barangUnit->save();
                    }

                    // Update status pengembalian
                    $peminjamanUnit->status_pengembalian = 'dikembalikan';
                    $peminjamanUnit->save();

                    $jumlahDikembalikan++;
                }

                // Kembalikan stok barang
                if ($jumlahDikembalikan > 0) {
                    $detail->barang->increment('stok', $jumlahDikembalikan);
                }
            }

            // Buat history pengembalian
            HistoryPeminjaman::create([
                'peminjaman_id' => $peminjaman->id,
                'user_id' => $peminjaman->user_id,
                'tanggal_kembali' => now(),
                'status_pengembalian' => 'Aman',
                'deskripsi_kerusakan' => 'Diselesaikan oleh admin',
            ]);

            DB::commit();

            return redirect()->route('admin.status.index')->with('success', 'Peminjaman berhasil diselesaikan dan barang dikembalikan ke stok.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error selesaikan peminjaman: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyelesaikan peminjaman: ' . $e->getMessage());
        }
    }

    /**
     * Membatalkan peminjaman (status Menunggu Konfirmasi)
     * Mengembalikan unit ke status baik dan menghapus data peminjaman
     */
    public function batalkan($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);

            // Validasi status harus Menunggu Konfirmasi
            if ($peminjaman->status !== 'Menunggu Konfirmasi') {
                return redirect()->back()->with('error', 'Peminjaman hanya bisa dibatalkan jika statusnya Menunggu Konfirmasi.');
            }

            DB::beginTransaction();

            // Ambil semua detail peminjaman
            $detailPeminjamans = DetailPeminjaman::where('peminjaman_id', $peminjaman->id)->get();

            foreach ($detailPeminjamans as $detail) {
                // Ambil semua unit yang dipinjam
                $peminjamanUnits = PeminjamanUnit::where('detail_peminjaman_id', $detail->id)->get();

                foreach ($peminjamanUnits as $peminjamanUnit) {
                    // Kembalikan status unit barang menjadi 'baik'
                    $barangUnit = BarangUnit::find($peminjamanUnit->barang_unit_id);
                    if ($barangUnit) {
                        $barangUnit->status = 'baik';
                        $barangUnit->save();
                    }

                    // Hapus peminjaman unit
                    $peminjamanUnit->delete();
                }

                // Hapus detail peminjaman
                $detail->delete();
            }

            // Hapus peminjaman
            $peminjaman->delete();

            DB::commit();

            return redirect()->route('admin.status.index')->with('success', 'Peminjaman berhasil dibatalkan dan barang dikembalikan ke stok.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error batalkan peminjaman: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membatalkan peminjaman: ' . $e->getMessage());
        }
    }
}
