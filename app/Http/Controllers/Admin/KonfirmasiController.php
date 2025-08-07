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
        $peminjamanMenunggu = Peminjaman::with('user')
            ->where('status', 'Menunggu Konfirmasi')
            ->latest()->get();

        $pengembalianMenunggu = Peminjaman::with('user', 'history')
            ->where('status', 'Tunggu Konfirmasi Admin')
            ->latest()->get();

        return view('admin.konfirmasi.index', compact('peminjamanMenunggu', 'pengembalianMenunggu'));
    }

    /**
     * Menampilkan detail peminjaman (untuk modal).
     */
    public function show($id)
    {
        $peminjaman = Peminjaman::with([
            'user',
            'detailPeminjamans.barang',
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
        $peminjaman = Peminjaman::with('detailPeminjamans.barang')->where('id', $id)->where('status', 'Menunggu Konfirmasi')->firstOrFail();

        DB::transaction(function () use ($peminjaman) {
            // 1. Kembalikan stok barang
            foreach ($peminjaman->detailPeminjamans as $detail) {
                $detail->barang->stok += $detail->jumlah;
                $detail->barang->save();
            }
            // 2. Hapus detail peminjaman terlebih dahulu
            $peminjaman->detailPeminjamans()->delete();
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
        $peminjaman = Peminjaman::with('detailPeminjamans.barang')->where('id', $id)->where('status', 'Tunggu Konfirmasi Admin')->firstOrFail();

        DB::transaction(function () use ($peminjaman) {
            // 1. Kembalikan stok barang yang dikembalikan (tidak termasuk yang hilang)
            foreach ($peminjaman->detailPeminjamans as $detail) {
                // `jumlah` di sini adalah jumlah yang benar-benar dikembalikan
                $detail->barang->stok += $detail->jumlah; 
                $detail->barang->save();
            }

            // 2. Ubah status peminjaman menjadi "Dikembalikan"
            $peminjaman->status = 'Dikembalikan';
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
        // Ambil data peminjaman beserta detail dan history-nya
        $peminjaman = Peminjaman::with('detailPeminjamans', 'history')
            ->where('id', $id)
            ->where('status', 'Tunggu Konfirmasi Admin')
            ->firstOrFail();

        // Gunakan transaksi database untuk memastikan semua operasi berhasil
        DB::transaction(function () use ($peminjaman) {
            
            // 1. Kembalikan data di setiap detail peminjaman ke kondisi semula
            foreach ($peminjaman->detailPeminjamans as $detail) {
                // Kembalikan jumlah pinjaman ke nilai awal
                // Nilai Awal = Jumlah yang Dikembalikan + Jumlah yang Hilang
                $detail->jumlah += $detail->jumlah_hilang;

                // Reset jumlah yang hilang menjadi 0
                $detail->jumlah_hilang = 0;
                $detail->save();
            }

            // 2. Hapus history pengembalian yang salah/dibatalkan
            if ($peminjaman->history) {
                $peminjaman->history()->delete();
            }

            // 3. Ubah status peminjaman kembali ke "Dipinjam"
            $peminjaman->status = 'Dipinjam';
            
            // 4. Kosongkan tanggal kembali karena pengembalian dibatalkan
            $peminjaman->tanggal_kembali = null;
            $peminjaman->save();
        });
        
        return back()->with('success', 'Pengembalian ditolak. Status dan jumlah barang telah dikembalikan seperti semula.');
    }
}