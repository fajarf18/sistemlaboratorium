<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\PeminjamanUnit;
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
        $peminjamanMenunggu = Peminjaman::with('user', 'detailPeminjaman.barang', 'dosenPengampu')
            ->where('status', 'Menunggu Konfirmasi')
            ->latest()->get();

        $pengembalianMenunggu = Peminjaman::with([
            'user',
            'dosenPengampu',
            'detailPeminjaman.barang',
            'detailPeminjaman.peminjamanUnits.barangUnit'
        ])
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
            'dosenPengampu',
            'detailPeminjaman.barang',
            'detailPeminjaman.peminjamanUnits.barangUnit',
            'history'
        ])->findOrFail($id);

        // ================= LOGIKA UNTUK PENGEMBALIAN =================
        if ($peminjaman->status === 'Tunggu Konfirmasi Admin') {

            $statusParts = [];
            $daysLate = 0;
            $totalHilang = 0; // Unit barang yang hilang/rusak (Tidak Habis Pakai)
            $totalHabis = 0;  // Unit barang yang digunakan (Habis Pakai)

            // Langkah 1: Iterasi semua detail dan unit untuk menghitung status aktual
            foreach ($peminjaman->detailPeminjaman as $detail) {
                foreach ($detail->peminjamanUnits as $peminjamanUnit) {
                    // Cek status pengembalian dari setiap unit
                    $statusPengembalian = $this->normalizeDamageStatus($peminjamanUnit->status_pengembalian);
                    if ($this->isDamageStatus($statusPengembalian)) {
                        // Bedakan berdasarkan tipe barang
                        if (strtolower($detail->barang->tipe) === 'habis pakai') {
                            $totalHabis++;
                        } else {
                            $totalHilang++;
                        }
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

            // Tambahkan data hasil perhitungan ke respons JSON
            $peminjaman->status_pengembalian = $status;
            $peminjaman->hari_terlambat = $daysLate;
            $peminjaman->total_hilang = $totalHilang;
            $peminjaman->total_habis = $totalHabis;
        }

        // ================= LOGIKA UNTUK PEMINJAMAN =================
        // Tambahkan informasi unit yang akan dipinjam untuk modal peminjaman
        if ($peminjaman->status === 'Menunggu Konfirmasi') {
            $peminjaman->total_unit_dipinjam = 0;
            foreach ($peminjaman->detailPeminjaman as $detail) {
                $peminjaman->total_unit_dipinjam += $detail->peminjamanUnits->count();
            }
        }

        return response()->json($peminjaman);
    }
    
    // Metode lainnya (terimaPeminjaman, tolakPeminjaman, dll.) tetap sama...

    public function terimaPeminjaman($id)
    {
        $peminjaman = Peminjaman::with('detailPeminjaman.peminjamanUnits.barangUnit')
            ->where('id', $id)
            ->where('status', 'Menunggu Konfirmasi')
            ->firstOrFail();

        DB::transaction(function () use ($peminjaman) {
            // Update status peminjaman
            $peminjaman->status = 'Dipinjam';
            $peminjaman->save();

            // Update status setiap unit barang menjadi 'dipinjam'
            foreach ($peminjaman->detailPeminjaman as $detail) {
                foreach ($detail->peminjamanUnits as $peminjamanUnit) {
                    $peminjamanUnit->barangUnit->update([
                        'status' => 'dipinjam'
                    ]);
                }
            }
        });

        return back()->with('success', 'Peminjaman berhasil dikonfirmasi.');
    }

    public function tolakPeminjaman($id)
    {
        $peminjaman = Peminjaman::with('detailPeminjaman.barang', 'detailPeminjaman.peminjamanUnits.barangUnit')
            ->where('id', $id)
            ->where('status', 'Menunggu Konfirmasi')
            ->firstOrFail();

        DB::transaction(function () use ($peminjaman) {
            // Kembalikan stok barang
            foreach ($peminjaman->detailPeminjaman as $detail) {
                $detail->barang->increment('stok', $detail->jumlah);

                // Reset status unit barang kembali ke 'baik'
                foreach ($detail->peminjamanUnits as $peminjamanUnit) {
                    $peminjamanUnit->barangUnit->update([
                        'status' => 'baik',
                        'keterangan' => null
                    ]);
                }
            }

            // Hapus detail peminjaman dan peminjaman units
            foreach ($peminjaman->detailPeminjaman as $detail) {
                $detail->peminjamanUnits()->delete();
            }
            $peminjaman->detailPeminjaman()->delete();
            $peminjaman->delete();
        });

        return back()->with('danger', 'Peminjaman telah ditolak dan stok dikembalikan.');
    }

    public function terimaPengembalian($id)
    {
        $peminjaman = Peminjaman::with('detailPeminjaman.peminjamanUnits.barangUnit')
            ->where('id', $id)
            ->where('status', 'Tunggu Konfirmasi Admin')
            ->firstOrFail();

        DB::transaction(function () use ($peminjaman) {
            foreach ($peminjaman->detailPeminjaman as $detail) {
                // Hitung jumlah per status dari peminjaman units
                $jumlahDikembalikan = 0;
                $jumlahRusakHilang = 0;

                foreach ($detail->peminjamanUnits as $peminjamanUnit) {
                    $statusPengembalian = $this->normalizeDamageStatus($peminjamanUnit->status_pengembalian);

                    if ($statusPengembalian === 'dikembalikan') {
                        $jumlahDikembalikan++;

                        // Update status unit barang ke tersedia (baik)
                        $peminjamanUnit->barangUnit->update([
                            'status' => 'baik',
                            'keterangan' => null
                        ]);
                    } elseif ($this->isDamageStatus($statusPengembalian)) {
                        // Rusak atau hilang
                        $jumlahRusakHilang++;

                        // Update status unit barang sesuai kondisi
                        $peminjamanUnit->barangUnit->update([
                            'status' => $statusPengembalian,
                            'keterangan' => $peminjamanUnit->keterangan_kondisi
                        ]);
                    }
                }

                // Update jumlah_hilang di detail peminjaman untuk history
                $detail->jumlah_hilang = $jumlahRusakHilang;
                $detail->save();

                // Tambahkan stok untuk unit yang dikembalikan baik saja
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
        $peminjaman = Peminjaman::with([
            'detailPeminjaman.peminjamanUnits.barangUnit',
            'history'
        ])
            ->where('id', $id)
            ->where('status', 'Tunggu Konfirmasi Admin')
            ->firstOrFail();

        DB::transaction(function () use ($peminjaman) {
            // Reset status unit yang sudah diubah
            foreach ($peminjaman->detailPeminjaman as $detail) {
                // Reset jumlah_hilang jika ada
                if (isset($detail->jumlah_hilang) && $detail->jumlah_hilang > 0) {
                    $detail->jumlah += $detail->jumlah_hilang;
                    $detail->jumlah_hilang = 0;
                    $detail->save();
                }

                // Reset status setiap unit peminjaman
                foreach ($detail->peminjamanUnits as $peminjamanUnit) {
                    // Reset status pengembalian ke belum dikembalikan
                    $peminjamanUnit->status_pengembalian = 'belum';
                    $peminjamanUnit->keterangan_kondisi = null;

                    // Hapus foto kondisi jika ada
                    if ($peminjamanUnit->foto_kondisi) {
                        \Storage::disk('public')->delete($peminjamanUnit->foto_kondisi);
                        $peminjamanUnit->foto_kondisi = null;
                    }

                    $peminjamanUnit->save();

                    // Reset status unit barang kembali ke dipinjam
                    $peminjamanUnit->barangUnit->update([
                        'status' => 'dipinjam',
                        'keterangan' => null
                    ]);
                }
            }

            // Hapus history pengembalian dan gambar bukti
            if ($peminjaman->history) {
                if ($peminjaman->history->gambar_bukti) {
                    \Storage::disk('public')->delete($peminjaman->history->gambar_bukti);
                }
                $peminjaman->history()->delete();
            }

            // Kembalikan status peminjaman ke Dipinjam
            $peminjaman->status = 'Dipinjam';
            $peminjaman->tanggal_kembali = null;
            $peminjaman->save();
        });

        return back()->with('danger', 'Pengembalian ditolak. Status peminjaman, unit, dan data telah dikembalikan seperti semula.');
    }

    private function normalizeDamageStatus(?string $status): ?string
    {
        if ($status === null) {
            return null;
        }

        $map = [
            'rusak' => 'rusak_ringan',
            'hilang' => 'rusak_berat',
        ];

        return $map[$status] ?? $status;
    }

    private function isDamageStatus(?string $status): bool
    {
        return in_array($status, ['rusak_ringan', 'rusak_berat'], true);
    }
}
