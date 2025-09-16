<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KonfirmasiController extends Controller
{
    /**
     * Menampilkan halaman utama konfirmasi.
     */
    public function index()
    {
        $peminjamanMenunggu = Peminjaman::with('user', 'detailPeminjaman.barang')
            ->where('status', 'Menunggu Konfirmasi')
            ->latest()->get();

        $pengembalianMenunggu = Peminjaman::with('user', 'detailPeminjaman.barang')
            ->where('status', 'Tunggu Konfirmasi Admin')
            ->latest()->get();

        return view('admin.konfirmasi.index', compact('peminjamanMenunggu', 'pengembalianMenunggu'));
    }

  /**
     * Menampilkan detail peminjaman untuk modal.
     */
    public function show($id)
    {
        $peminjaman = Peminjaman::with([
            'user',
            'detailPeminjaman.barang', 
            'history'
        ])->findOrFail($id);

        // Hanya jalankan logika ini jika ini adalah proses konfirmasi pengembalian
        if ($peminjaman->status === 'Tunggu Konfirmasi Admin') {
            
            // ================= LOGIKA BARU SESUAI PERMINTAAN =================
            
            // Langkah 1: Inisialisasi variabel
            $statusParts = [];
            $daysLate = 0;
            $totalLost = $peminjaman->detailPeminjaman->sum(fn($detail) => (int) $detail->jumlah_hilang);

            // Langkah 2: Evaluasi kondisi "Hilang"
            if ($totalLost > 0) {
                $statusParts[] = 'Hilang';
            }

            // Langkah 3: Evaluasi kondisi "Terlambat"
            // Pastikan ada tanggal kembali yang diinput oleh user untuk dibandingkan
            if ($peminjaman->tanggal_kembali) {
                // Tentukan tanggal wajib kembali (3 hari setelah pinjam)
                $tanggalPinjam = Carbon::parse($peminjaman->tanggal_pinjam)->startOfDay();
                $tanggalWajibKembali = $tanggalPinjam->copy()->addDays(3);
                
                // Ambil tanggal pengembalian AKTUAL dari input user di tabel peminjaman
                $tanggalPengembalianUser = Carbon::parse($peminjaman->tanggal_kembali)->startOfDay();

                // Bandingkan tanggal pengembalian user dengan tanggal wajib kembali
                if ($tanggalPengembalianUser->isAfter($tanggalWajibKembali)) {
                    $statusParts[] = 'Terlambat';
                    // Hitung selisih hari keterlambatan
                    $daysLate = $tanggalWajibKembali->diffInDays($tanggalPengembalianUser);
                }
            }

            // Langkah 4: Gabungkan hasil evaluasi untuk status final
            $status = !empty($statusParts) ? implode(' dan ', $statusParts) : 'Aman';
            // ====================================================================

            // Tambahkan data hasil perhitungan ke respons JSON
            $peminjaman->status_pengembalian = $status;
            $peminjaman->hari_terlambat = $daysLate;
            $peminjaman->total_hilang = $totalLost;
        }

        return response()->json($peminjaman);
    }
    
    // Metode lainnya (terimaPeminjaman, tolakPeminjaman, dll.) tetap sama...

    public function terimaPeminjaman($id)
    {
        $peminjaman = Peminjaman::where('id', $id)->where('status', 'Menunggu Konfirmasi')->firstOrFail();
        $peminjaman->status = 'Dipinjam';
        $peminjaman->save();
        return back()->with('success', 'Peminjaman berhasil dikonfirmasi.');
    }

    public function tolakPeminjaman($id)
    {
        $peminjaman = Peminjaman::with('detailPeminjaman.barang')->where('id', $id)->where('status', 'Menunggu Konfirmasi')->firstOrFail();

        DB::transaction(function () use ($peminjaman) {
            foreach ($peminjaman->detailPeminjaman as $detail) {
                $detail->barang->increment('stok', $detail->jumlah);
            }
            $peminjaman->detailPeminjaman()->delete();
            $peminjaman->delete();
        });

        return back()->with('danger', 'Peminjaman telah ditolak dan stok dikembalikan.');
    }

    public function terimaPengembalian($id)
    {
        $peminjaman = Peminjaman::with('detailPeminjaman.barang')->where('id', $id)->where('status', 'Tunggu Konfirmasi Admin')->firstOrFail();

        DB::transaction(function () use ($peminjaman) {
            foreach ($peminjaman->detailPeminjaman as $detail) {
                $jumlahDikembalikan = $detail->jumlah - ($detail->jumlah_hilang ?? 0);
                if ($jumlahDikembalikan > 0) {
                    $detail->barang->increment('stok', $jumlahDikembalikan);
                }
            }
            $peminjaman->status = 'Dikembalikan';
            $peminjaman->tanggal_kembali = now();
            $peminjaman->save();
        });

        return back()->with('success', 'Pengembalian berhasil dikonfirmasi dan stok telah diperbarui.');
    }

    public function tolakPengembalian($id)
    {
        $peminjaman = Peminjaman::with('detailPeminjaman', 'history')
            ->where('id', $id)
            ->where('status', 'Tunggu Konfirmasi Admin')
            ->firstOrFail();

        DB::transaction(function () use ($peminjaman) {
            foreach ($peminjaman->detailPeminjaman as $detail) {
                if (isset($detail->jumlah_hilang) && $detail->jumlah_hilang > 0) {
                    $detail->jumlah += $detail->jumlah_hilang;
                    $detail->jumlah_hilang = 0;
                    $detail->save();
                }
            }
            if ($peminjaman->history) {
                $peminjaman->history()->delete();
            }
            $peminjaman->status = 'Dipinjam';
            $peminjaman->tanggal_kembali = null;
            $peminjaman->save();
        });
        
        return back()->with('danger', 'Pengembalian ditolak. Status dan jumlah barang telah dikembalikan seperti semula.');
    }
}