<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KonfirmasiController extends Controller
{
    /**
     * Menampilkan halaman utama konfirmasi dengan dua daftar.
     */
    public function index()
    {
        // --- PERBAIKAN 1: Tambahkan 'with()' untuk memuat detail barang ---
        $peminjamanMenunggu = Peminjaman::with('user', 'detailPeminjaman.barang')
            ->where('status', 'Menunggu Konfirmasi')
            ->latest()->get();

        $pengembalianMenunggu = Peminjaman::with('user', 'detailPeminjaman.barang')
            ->where('status', 'Tunggu Konfirmasi Admin')
            ->latest()->get();

        return view('admin.konfirmasi.index', compact('peminjamanMenunggu', 'pengembalianMenunggu'));
    }

    /**
     * Menampilkan detail peminjaman (untuk modal).
     */
    public function show($id)
    {
        // --- PERBAIKAN 2: Sesuaikan nama relasi menjadi 'detailPeminjaman' ---
        $peminjaman = Peminjaman::with([
            'user',
            'detailPeminjaman.barang', 
            'history'
        ])->findOrFail($id);
        
        return response()->json($peminjaman);
    }

    /**
     * Menyetujui permintaan peminjaman.
     * Status: "Menunggu Konfirmasi" -> "Dipinjam"
     */
    public function terimaPeminjaman($id)
    {
        $peminjaman = Peminjaman::where('id', $id)->where('status', 'Menunggu Konfirmasi')->firstOrFail();
        $peminjaman->status = 'Dipinjam';
        $peminjaman->save();
        return back()->with('success', 'Peminjaman berhasil dikonfirmasi.');
    }

    /**
     * Menolak permintaan peminjaman.
     * Stok dikembalikan dan data peminjaman dihapus.
     */
    public function tolakPeminjaman($id)
    {
        // Menggunakan nama relasi yang benar
        $peminjaman = Peminjaman::with('detailPeminjaman.barang')->where('id', $id)->where('status', 'Menunggu Konfirmasi')->firstOrFail();

        DB::transaction(function () use ($peminjaman) {
            // 1. Kembalikan stok barang
            foreach ($peminjaman->detailPeminjaman as $detail) {
                $detail->barang->increment('stok', $detail->jumlah);
            }
            // 2. Hapus detail peminjaman terlebih dahulu
            $peminjaman->detailPeminjaman()->delete();
            // 3. Hapus peminjaman utama
            $peminjaman->delete();
        });

        return back()->with('success', 'Peminjaman telah ditolak dan stok dikembalikan.');
    }

    /**
     * Menyetujui permintaan pengembalian.
     * Stok barang yang tidak hilang dikembalikan, dan status menjadi "Dikembalikan".
     */
    public function terimaPengembalian($id)
    {
        // Menggunakan nama relasi yang benar
        $peminjaman = Peminjaman::with('detailPeminjaman.barang')->where('id', $id)->where('status', 'Tunggu Konfirmasi Admin')->firstOrFail();

        DB::transaction(function () use ($peminjaman) {
            // 1. Kembalikan stok barang yang dikembalikan
            foreach ($peminjaman->detailPeminjaman as $detail) {
                $jumlahDikembalikan = $detail->jumlah - ($detail->jumlah_hilang ?? 0);
                if ($jumlahDikembalikan > 0) {
                    $detail->barang->increment('stok', $jumlahDikembalikan);
                }
            }

            // 2. Ubah status peminjaman menjadi "Selesai"
            $peminjaman->status = 'Dikembalikan';
            $peminjaman->tanggal_kembali = now();
            $peminjaman->save();
        });

        return back()->with('success', 'Pengembalian berhasil dikonfirmasi dan stok telah diperbarui.');
    }

    /**
     * Menolak permintaan pengembalian.
     * Status: "Tunggu Konfirmasi Admin" -> "Dipinjam"
     */
    public function tolakPengembalian($id)
    {
        // Menggunakan nama relasi yang benar
        $peminjaman = Peminjaman::with('detailPeminjaman', 'history')
            ->where('id', $id)
            ->where('status', 'Tunggu Konfirmasi Admin')
            ->firstOrFail();

        DB::transaction(function () use ($peminjaman) {
            
            // 1. Kembalikan data di detail peminjaman ke kondisi semula
            foreach ($peminjaman->detailPeminjaman as $detail) {
                if (isset($detail->jumlah_hilang) && $detail->jumlah_hilang > 0) {
                    $detail->jumlah += $detail->jumlah_hilang;
                    $detail->jumlah_hilang = 0;
                    $detail->save();
                }
            }

            // 2. Hapus history pengembalian yang salah/dibatalkan
            if ($peminjaman->history) {
                $peminjaman->history()->delete();
            }

            // 3. Ubah status peminjaman kembali ke "Dipinjam"
            $peminjaman->status = 'Dipinjam';
            
            // 4. Kosongkan tanggal kembali
            $peminjaman->tanggal_kembali = null;
            $peminjaman->save();
        });
        
        return back()->with('success', 'Pengembalian ditolak. Status dan jumlah barang telah dikembalikan seperti semula.');
    }
}

