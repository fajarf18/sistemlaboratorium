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

        if ($peminjaman->status === 'Tunggu Konfirmasi Admin') {
            
            // ================= LOGIKA BARU UNTUK PEMISAHAN TIPE =================
            
            $statusParts = [];
            $daysLate = 0;
            $totalHilang = 0; // Untuk barang 'Tidak Habis Pakai' yang kurang
            $totalHabis = 0;  // Untuk barang 'Habis Pakai' yang kurang

            // Langkah 1: Iterasi dan hitung total untuk setiap jenis barang
             foreach ($peminjaman->detailPeminjaman as $detail) {
                $jumlahKurang = (int) $detail->jumlah_hilang;
                if ($jumlahKurang > 0) {
                    
                    // ================= PERBAIKAN DI SINI =================
                    // Mengubah 'jenis' menjadi 'tipe' agar cocok dengan database Anda
                    if (strtolower($detail->barang->tipe) === 'habis pakai') {
                    // =======================================================
                        $totalHabis += $jumlahKurang;
                    } else {
                        $totalHilang += $jumlahKurang;
                    }
                }
            }
            
            // Langkah 2: Tentukan status berdasarkan hasil perhitungan
            if ($totalHilang > 0) $statusParts[] = 'Hilang';
            if ($totalHabis > 0) $statusParts[] = 'Habis';

            // Langkah 3: Evaluasi kondisi "Terlambat"
            if ($peminjaman->tanggal_kembali) {
                $tanggalPinjam = Carbon::parse($peminjaman->tanggal_pinjam)->startOfDay();
                $tanggalWajibKembali = $tanggalPinjam->copy()->addDays(3);
                $tanggalPengembalianUser = Carbon::parse($peminjaman->tanggal_kembali)->startOfDay();

                if ($tanggalPengembalianUser->isAfter($tanggalWajibKembali)) {
                    $statusParts[] = 'Terlambat';
                    $daysLate = $tanggalWajibKembali->diffInDays($tanggalPengembalianUser);
                }
            }

            // Langkah 4: Gabungkan status final
            $status = !empty($statusParts) ? implode(' dan ', $statusParts) : 'Aman';
            // =======================================================================

            // Tambahkan data hasil perhitungan ke respons JSON
            $peminjaman->status_pengembalian = $status;
            $peminjaman->hari_terlambat = $daysLate;
            $peminjaman->total_hilang = $totalHilang; // Sekarang ini HANYA untuk barang hilang
            $peminjaman->total_habis = $totalHabis;   // Properti baru untuk barang habis
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