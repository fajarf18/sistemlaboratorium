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
        $peminjamanMenunggu = Peminjaman::with('user', 'detailPeminjaman.barang', 'dosen', 'kelasPraktikum.modul')
            ->where('status', 'Menunggu Konfirmasi')
            ->latest()
            ->get();

        $pengembalianMenunggu = Peminjaman::with([
            'user',
            'dosen',
            'kelasPraktikum.creator',
            'detailPeminjaman.barang',
            'detailPeminjaman.peminjamanUnits.barangUnit'
        ])
            ->where('status', 'Tunggu Konfirmasi Admin')
            ->latest()
            ->get();

        // Mengambil peminjaman yang statusnya BUKAN 'Dikembalikan'
        $statusPeminjam = Peminjaman::with([
            'user',
            'dosen',
            'kelasPraktikum.modul',
            'detailPeminjaman.barang',
            'detailPeminjaman.peminjamanUnits.barangUnit'
        ])
        ->where('status', '!=', 'Dikembalikan') 
        ->latest('tanggal_pinjam')
        ->get();

        return view('admin.konfirmasi.index', compact('peminjamanMenunggu', 'pengembalianMenunggu', 'statusPeminjam'));
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
            'dosen',
            'kelasPraktikum.modul',
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
                if (strtolower($detail->barang->tipe) === 'habis pakai') {
                    // Detail habis pakai: gunakan jumlah_rusak sebagai jumlah yang sudah terpakai/habis
                    $totalHabis += (int) $detail->jumlah_rusak;
                    continue;
                }

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
            if ($totalHilang > 0) $statusParts[] = 'Rusak';
            // NOTE: Unit habis pakai yang berkurang tidak menyebabkan status pengembalian menjadi "Habis" atau "Rusak".
            // Konsumable ("Habis Pakai") yang berkurang akan dihapus dari unit dan mengurangi stok, tetapi
            // tidak mempengaruhi status pengembalian keseluruhan (dibiarkan sebagai "Aman" kecuali ada rusak non-konsumable atau terlambat).

            // Langkah 3: Evaluasi kondisi "Terlambat"
            if ($peminjaman->tanggal_kembali) {
                $tanggalPinjam = Carbon::parse($peminjaman->tanggal_pinjam)->startOfDay();
                // Gunakan tanggal_wajib_kembali jika tersedia, fallback ke +3 hari dari pinjam
                $tanggalWajibKembali = $peminjaman->tanggal_wajib_kembali
                    ? Carbon::parse($peminjaman->tanggal_wajib_kembali)->startOfDay()
                    : $tanggalPinjam->copy()->addDays(3);
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
                if (strtolower($detail->barang->tipe) === 'habis pakai') {
                    $peminjaman->total_unit_dipinjam += $detail->jumlah;
                } else {
                    $peminjaman->total_unit_dipinjam += $detail->peminjamanUnits->count();
                }
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
                if (strtolower($detail->barang->tipe) === 'habis pakai') {
                    $unitsDipinjam = $detail->barang->units()->where('status', 'dipinjam')->limit($detail->jumlah)->get();
                    foreach ($unitsDipinjam as $unit) {
                        $unit->update(['status' => 'baik', 'keterangan' => null]);
                    }
                } else {
                    foreach ($detail->peminjamanUnits as $peminjamanUnit) {
                        $peminjamanUnit->barangUnit->update([
                            'status' => 'baik',
                            'keterangan' => null
                        ]);
                    }
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
        $peminjaman = Peminjaman::with('detailPeminjaman.barang', 'detailPeminjaman.peminjamanUnits.barangUnit')
            ->where('id', $id)
            ->where('status', 'Tunggu Konfirmasi Admin')
            ->firstOrFail();

        DB::transaction(function () use ($peminjaman) {
            foreach ($peminjaman->detailPeminjaman as $detail) {
                // Khusus barang habis pakai: gunakan jumlah_rusak sebagai jumlah yang terpakai, tambahkan sisa ke stok
                if (strtolower($detail->barang->tipe) === 'habis pakai') {
                    $jumlahDipinjam = (int) $detail->jumlah;
                    $jumlahTerpakai = min($jumlahDipinjam, max(0, (int) $detail->jumlah_rusak));
                    $jumlahSisa = $jumlahDipinjam - $jumlahTerpakai;

                    // Unit yang sedang dipinjam (untuk habis pakai) diasumsikan bertatus 'dipinjam'
                    $dipinjamUnits = $detail->barang->units()->where('status', 'dipinjam')->limit($jumlahDipinjam)->get();

                    // Unit yang kembali (jumlah sisa) dikembalikan ke status baik
                    if ($jumlahSisa > 0) {
                        $unitsKembali = $dipinjamUnits->take($jumlahSisa);
                        foreach ($unitsKembali as $unit) {
                            $unit->update(['status' => 'baik']);
                        }
                        $detail->barang->increment('stok', $jumlahSisa);
                    }

                    // Unit yang terpakai dihapus dari master
                    if ($jumlahTerpakai > 0) {
                        $unitsHabis = $dipinjamUnits->slice($jumlahSisa, $jumlahTerpakai);
                        foreach ($unitsHabis as $unit) {
                            $unit->delete();
                        }
                    }

                    // Simpan jumlah terpakai sebagai riwayat
                    $detail->jumlah_rusak = $jumlahTerpakai;
                    $detail->save();

                    // Bersihkan peminjamanUnits/barangUnits jika pernah tercatat pada peminjaman ini (defensif)
                    foreach ($detail->peminjamanUnits as $peminjamanUnit) {
                        if ($peminjamanUnit->barangUnit) {
                            $peminjamanUnit->barangUnit->delete();
                        }
                        $peminjamanUnit->delete();
                    }
                    continue;
                }

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
                        // Jika barang ini adalah jenis "Habis Pakai", perlakukan unit yang berkurang sebagai
                        // "digunakan" -> hapus unit master dan kurangi stok, tetapi jangan hitung sebagai rusak/hilang
                        if (strtolower($detail->barang->tipe) === 'habis pakai') {
                            // Hapus unit master yang dipakai (jika ada)
                            if ($peminjamanUnit->barangUnit) {
                                // Hapus unit langsung (melewati controller) dan kurangi stok di tabel barang
                                $peminjamanUnit->barangUnit->delete();
                                $detail->barang->decrement('stok', 1);
                            }
                        } else {
                            // Non-konsumable: perlakukan sebagai rusak/hilang seperti biasa
                            $jumlahRusakHilang++;

                            // Update status unit barang sesuai kondisi
                            $peminjamanUnit->barangUnit->update([
                                'status' => $statusPengembalian,
                                'keterangan' => $peminjamanUnit->keterangan_kondisi
                            ]);
                        }
                    }
                }

                // Update jumlah_rusak di detail peminjaman untuk history
                $detail->jumlah_rusak = $jumlahRusakHilang;
                $detail->save();

                // Tambahkan stok untuk unit yang dikembalikan baik saja
                if ($jumlahDikembalikan > 0) {
                    $detail->barang->increment('stok', $jumlahDikembalikan);
                }
            }

            // Jangan timpa tanggal_kembali dari user; gunakan jika sudah diisi, fallback ke sekarang
            if (!$peminjaman->tanggal_kembali) {
                $peminjaman->tanggal_kembali = now();
            }
            $peminjaman->status = 'Dikembalikan';
            $peminjaman->save();
            // Reset kelas praktikum jika ada (supaya mahasiswa bisa join lagi)
            if ($peminjaman->kelas_praktikum_id) {
                $peminjaman->user->kelasPraktikumsJoined()->detach($peminjaman->kelas_praktikum_id);
            }

            return back()->with('success', 'Pengembalian berhasil dikonfirmasi, stok diperbarui, dan status kelas praktikum mahasiswa telah di-reset.');
        });

        // return back()->with('success', 'Pengembalian berhasil dikonfirmasi dan stok telah diperbarui.'); // Moved inside transaction/success message updated
        return back(); // The return is handled inside, or catch exception. Wait, DB::transaction returns result.
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
                // Reset data habis pakai yang disimpan sementara
                $detail->jumlah_rusak = 0;
                $detail->save();

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
            $detailPeminjamans = \App\Models\DetailPeminjaman::where('peminjaman_id', $peminjaman->id)->get();

            foreach ($detailPeminjamans as $detail) {
                // Ambil semua unit yang dipinjam
                $peminjamanUnits = PeminjamanUnit::where('detail_peminjaman_id', $detail->id)->get();

                $jumlahDikembalikan = 0;

                foreach ($peminjamanUnits as $peminjamanUnit) {
                    // Update status unit barang menjadi 'baik'
                    $barangUnit = \App\Models\BarangUnit::find($peminjamanUnit->barang_unit_id);
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
            \App\Models\HistoryPeminjaman::create([
                'peminjaman_id' => $peminjaman->id,
                'user_id' => $peminjaman->user_id,
                'tanggal_kembali' => now(),
                'status_pengembalian' => 'Aman',
                'deskripsi_kerusakan' => 'Diselesaikan oleh admin',
            ]);

            DB::commit();

            return redirect()->route('admin.konfirmasi.index')->with('success', 'Peminjaman berhasil diselesaikan dan barang dikembalikan ke stok.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error selesaikan peminjaman: ' . $e->getMessage());
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
            $detailPeminjamans = \App\Models\DetailPeminjaman::where('peminjaman_id', $peminjaman->id)->get();

            foreach ($detailPeminjamans as $detail) {
                // Ambil semua unit yang dipinjam
                $peminjamanUnits = PeminjamanUnit::where('detail_peminjaman_id', $detail->id)->get();

                foreach ($peminjamanUnits as $peminjamanUnit) {
                    // Kembalikan status unit barang menjadi 'baik'
                    $barangUnit = \App\Models\BarangUnit::find($peminjamanUnit->barang_unit_id);
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

            return redirect()->route('admin.konfirmasi.index')->with('success', 'Peminjaman berhasil dibatalkan dan barang dikembalikan ke stok.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error batalkan peminjaman: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membatalkan peminjaman: ' . $e->getMessage());
        }
    }
}
