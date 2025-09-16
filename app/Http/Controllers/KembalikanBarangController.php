<?php

namespace App\Http\Controllers;

use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use App\Models\HistoryPeminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ItemReturnNotification;
use Carbon\Carbon;

class KembalikanBarangController extends Controller
{
    public function index(Request $request)
    {
        $query = DetailPeminjaman::with(['barang', 'peminjaman'])
            ->whereHas('peminjaman', function ($q) {
                $q->where('user_id', Auth::id())
                  ->where('status', 'Dipinjam');
            });

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->whereHas('barang', function ($q) use ($searchTerm) {
                $q->where('nama_barang', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $detailPeminjamans = $query->get();
        return view('user.kembalikan-barang', compact('detailPeminjamans'));
    }

    public function konfirmasi(Request $request)
    {
        $items = json_decode($request->items, true);
        $adaBarangHilang = false;
        foreach ($items as $itemData) {
            if ((int)$itemData['jumlahDikembalikan'] < (int)$itemData['jumlah']) {
                $adaBarangHilang = true;
                break;
            }
        }

        $request->validate([
            'tanggal_kembali' => 'required|date',
            // Nama input di form adalah 'gambar_bukti'
            'gambar_bukti' => 'nullable|image|max:2048',
            'items' => 'required|json',
            'deskripsi_kehilangan' => $adaBarangHilang ? 'required|string' : 'nullable|string',
        ]);

        $userId = Auth::id();
        $isTerlambat = false;
        $peminjamanUntukNotifikasi = null;
        $peminjamanIdsToUpdate = [];

        DB::beginTransaction();
        try {
            // ================= PERBAIKAN LOGIKA GAMBAR =================
            // 1. Proses dan simpan gambar terlebih dahulu jika ada.
            $imagePath = null;
            if ($request->hasFile('gambar_bukti')) {
                // Simpan file di 'storage/app/public/bukti_pengembalian'
                $imagePath = $request->file('gambar_bukti')->store('bukti_pengembalian', 'public');
            }
            // ==========================================================

            foreach ($items as $itemData) {
                $detail = DetailPeminjaman::with('peminjaman', 'barang')
                    ->where('id', $itemData['id'])
                    ->whereHas('peminjaman', function ($q) use ($userId) {
                        $q->where('user_id', $userId)->where('status', 'Dipinjam');
                    })->firstOrFail();

                if (!$peminjamanUntukNotifikasi) {
                    $peminjamanUntukNotifikasi = $detail->peminjaman;
                }
                
                $peminjamanIdsToUpdate[] = $detail->peminjaman_id;
                
                $jumlahDipinjam = (int)$itemData['jumlah'];
                $jumlahDikembalikan = (int)$itemData['jumlahDikembalikan'];

                if ($jumlahDikembalikan < $jumlahDipinjam) {
                    $jumlahHilang = $jumlahDipinjam - $jumlahDikembalikan;
                    $detail->jumlah = $jumlahDikembalikan;
                    $detail->jumlah_hilang = ($detail->jumlah_hilang ?? 0) + $jumlahHilang; 
                    $detail->save();
                }
            }
            
            if (!empty($peminjamanIdsToUpdate)) {
                Peminjaman::whereIn('id', array_unique($peminjamanIdsToUpdate))
                    ->update([
                        'status' => 'Tunggu Konfirmasi Admin',
                        'tanggal_kembali' => $request->tanggal_kembali
                    ]);
            }

            $tanggalKembaliCarbon = Carbon::parse($request->tanggal_kembali);
            $tanggalWajibKembaliCarbon = Carbon::parse($peminjamanUntukNotifikasi->tanggal_pinjam)->addDays(3);

            if ($tanggalKembaliCarbon->isAfter($tanggalWajibKembaliCarbon)) {
                $isTerlambat = true;
            }

            $statusPengembalian = 'Aman';
            if ($adaBarangHilang && $isTerlambat) {
                $statusPengembalian = 'Hilang dan Terlambat';
            } elseif ($adaBarangHilang) {
                $statusPengembalian = 'Hilang';
            } elseif ($isTerlambat) {
                $statusPengembalian = 'Terlambat';
            }

            // 2. Buat history record dengan path gambar yang sudah diproses.
            HistoryPeminjaman::create([
                'peminjaman_id' => $peminjamanUntukNotifikasi->id,
                'user_id' => $userId,
                'tanggal_kembali' => $request->tanggal_kembali,
                'status_pengembalian' => $statusPengembalian,
                'deskripsi_kehilangan' => $adaBarangHilang ? $request->deskripsi_kehilangan : null,
                'gambar_bukti' => $imagePath, // Gunakan variabel path yang sudah disiapkan
            ]);

            DB::commit();

            try {
                $peminjamanLengkap = Peminjaman::with('user', 'detailPeminjaman.barang')->find($peminjamanUntukNotifikasi->id);
                if ($peminjamanLengkap) {
                    Mail::to(config('app.admin_email'))->send(new ItemReturnNotification($peminjamanLengkap));
                }
            } catch (\Exception $e) {
                \Log::error('Gagal mengirim email notifikasi pengembalian: '. $e->getMessage());
            }

            // Arahkan ke rute history yang benar
            return redirect()->route('user.history.index')->with('pengembalian_sukses', true);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengembalikan barang: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | METHOD LAMA (DIARSIPKAN)
    |--------------------------------------------------------------------------
    |
    | Method 'kembalikan($id)' ini adalah alur lama yang lebih sederhana.
    | Alur utama sekarang ditangani oleh method 'konfirmasi()'.
    | Method ini bisa dihapus jika sudah tidak ada tombol/link yang memanggilnya.
    |
    */
    public function kembalikan($id)
    {
        $peminjaman = Peminjaman::with('user', 'detailPeminjaman.barang')->findOrFail($id);
        if ($peminjaman->user_id !== auth()->id()) {
            abort(403, 'Anda tidak diizinkan melakukan aksi ini.');
        }
        $peminjaman->status = 'Tunggu Konfirmasi Admin';
        $peminjaman->save();
        
        try {
            Mail::to(config('app.admin_email'))->send(new ItemReturnNotification($peminjaman));
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim email notifikasi pengembalian dari method lama: '. $e->getMessage());
        }
        
        // Pastikan nama rute ini benar, sepertinya yang benar adalah 'user.history.index'
        return redirect()->route('history-peminjaman.index')->with('success', 'Pengajuan pengembalian barang berhasil, tunggu konfirmasi dari admin.');
    }
}