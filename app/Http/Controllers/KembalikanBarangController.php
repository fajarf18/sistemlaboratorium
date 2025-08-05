<?php

namespace App\Http\Controllers;

use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use App\Models\HistoryPeminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            'gambar_bukti' => 'nullable|image|max:2048',
            'items' => 'required|json',
            'deskripsi_kehilangan' => $adaBarangHilang ? 'required|string' : 'nullable|string',
        ]);

        $userId = Auth::id();
        $isTerlambat = false;
        $peminjamanUntukHistory = null;
        $peminjamanIdsToUpdate = [];

        DB::beginTransaction();
        try {
            foreach ($items as $itemData) {
                $detail = DetailPeminjaman::with('peminjaman', 'barang')
                    ->where('id', $itemData['id'])
                    ->whereHas('peminjaman', function ($q) use ($userId) {
                        $q->where('user_id', $userId)->where('status', 'Dipinjam');
                    })->firstOrFail();

                if (!$peminjamanUntukHistory) {
                    $peminjamanUntukHistory = $detail->peminjaman;
                }
                
                $peminjamanIdsToUpdate[] = $detail->peminjaman_id;
                
                // LOGIKA PENAMBAHAN STOK DIHAPUS DARI SINI
                // Stok akan dikembalikan oleh admin setelah konfirmasi
            }
            
            if (!empty($peminjamanIdsToUpdate)) {
                // UBAH STATUS MENJADI TUNGGU KONFIRMASI ADMIN
                Peminjaman::whereIn('id', array_unique($peminjamanIdsToUpdate))
                    ->update([
                        'status' => 'Tunggu Konfirmasi Admin',
                        'tanggal_kembali' => $request->tanggal_kembali
                    ]);
            }

            $tanggalKembaliCarbon = Carbon::parse($request->tanggal_kembali);
            $tanggalWajibKembaliCarbon = Carbon::parse($peminjamanUntukHistory->tanggal_wajib_kembali);

            if ($tanggalKembaliCarbon->isAfter($tanggalWajibKembaliCarbon)) {
                $isTerlambat = true;
            }

            $statusPengembalian = 'Aman';
            if ($adaBarangHilang) $statusPengembalian = 'Hilang';
            if ($isTerlambat) $statusPengembalian = $adaBarangHilang ? 'Hilang dan Terlambat' : 'Terlambat';

            $history = HistoryPeminjaman::create([
                'peminjaman_id' => $peminjamanUntukHistory->id,
                'user_id' => $userId,
                'tanggal_kembali' => $request->tanggal_kembali,
                'status_pengembalian' => $statusPengembalian,
                'deskripsi_kehilangan' => $adaBarangHilang ? $request->deskripsi_kehilangan : null,
                'gambar_bukti' => null,
            ]);
            
            if ($request->hasFile('gambar_bukti')) {
                $path = $request->file('gambar_bukti')->store('bukti_pengembalian', 'public');
                $history->gambar_bukti = $path;
                $history->save();
            }

            DB::commit();
            return redirect()->route('user.history.index')->with('pengembalian_sukses', true);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengembalikan barang: ' . $e->getMessage());
        }
    }
}