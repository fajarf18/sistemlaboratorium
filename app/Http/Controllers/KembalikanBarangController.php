<?php

namespace App\Http\Controllers;

use App\Models\BarangUnit;
use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use App\Models\PeminjamanUnit;
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
        $query = DetailPeminjaman::with(['barang', 'peminjaman', 'peminjamanUnits.barangUnit'])
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
        $request->validate([
            'tanggal_kembali' => 'required|date',
            'gambar_bukti' => 'nullable|image|max:2048',
            'unit_statuses' => 'required|json',
        ]);

        $unitStatuses = json_decode($request->unit_statuses, true);

        // Validasi format unit statuses
        if (!is_array($unitStatuses) || empty($unitStatuses)) {
            return back()->with('error', 'Data status unit tidak valid. Harap pilih status untuk semua unit.');
        }

        // Validasi setiap status unit
        foreach ($unitStatuses as $unitId => $statusData) {
            if (!isset($statusData['status']) || !in_array($statusData['status'], ['dikembalikan', 'rusak', 'hilang'])) {
                return back()->with('error', 'Status unit tidak valid untuk unit ID: ' . $unitId);
            }

            // Jika rusak atau hilang, keterangan wajib diisi
            if (in_array($statusData['status'], ['rusak', 'hilang'])) {
                if (empty($statusData['keterangan'])) {
                    return back()->with('error', 'Keterangan wajib diisi untuk unit yang rusak atau hilang.');
                }
            }
        }

        $userId = Auth::id();
        $isTerlambat = false;
        $peminjamanUntukNotifikasi = null;
        $peminjamanIdsToUpdate = [];
        $adaBarangRusakHilang = false;

        DB::beginTransaction();
        try {
            // Proses gambar bukti pengembalian jika ada
            $imagePath = null;
            if ($request->hasFile('gambar_bukti')) {
                $imagePath = $request->file('gambar_bukti')->store('bukti_pengembalian', 'public');
            }

            // Update status setiap unit yang dipinjam
            foreach ($unitStatuses as $unitId => $statusData) {
                $peminjamanUnit = PeminjamanUnit::with('detailPeminjaman.peminjaman', 'barangUnit')
                    ->where('id', $unitId)
                    ->whereHas('detailPeminjaman.peminjaman', function ($q) use ($userId) {
                        $q->where('user_id', $userId)->where('status', 'Dipinjam');
                    })->firstOrFail();

                if (!$peminjamanUntukNotifikasi) {
                    $peminjamanUntukNotifikasi = $peminjamanUnit->detailPeminjaman->peminjaman;
                }

                $peminjamanIdsToUpdate[] = $peminjamanUnit->detailPeminjaman->peminjaman_id;

                // Update status pengembalian unit
                $peminjamanUnit->status_pengembalian = $statusData['status'];

                // Jika rusak atau hilang, simpan foto dan keterangan
                if (in_array($statusData['status'], ['rusak', 'hilang'])) {
                    $adaBarangRusakHilang = true;
                    $peminjamanUnit->keterangan_kondisi = $statusData['keterangan'] ?? null;

                    // Upload foto kondisi jika ada
                    if (isset($statusData['foto']) && $statusData['foto']) {
                        // Handle base64 image from frontend
                        $fotoPath = $this->saveBase64Image($statusData['foto'], 'kondisi_unit');
                        $peminjamanUnit->foto_kondisi = $fotoPath;
                    }
                }
                // CATATAN: Status unit barang di master data TIDAK diupdate di sini
                // Status unit akan diupdate oleh admin saat menerima/menolak pengembalian

                $peminjamanUnit->save();
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
            if ($adaBarangRusakHilang && $isTerlambat) {
                $statusPengembalian = 'Rusak/Hilang dan Terlambat';
            } elseif ($adaBarangRusakHilang) {
                $statusPengembalian = 'Rusak/Hilang';
            } elseif ($isTerlambat) {
                $statusPengembalian = 'Terlambat';
            }

            // Buat history record
            HistoryPeminjaman::create([
                'peminjaman_id' => $peminjamanUntukNotifikasi->id,
                'user_id' => $userId,
                'tanggal_kembali' => $request->tanggal_kembali,
                'status_pengembalian' => $statusPengembalian,
                'deskripsi_kehilangan' => $adaBarangRusakHilang ? 'Ada unit rusak/hilang, lihat detail unit' : null,
                'gambar_bukti' => $imagePath,
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

    /**
     * Save base64 encoded image to storage
     */
    private function saveBase64Image($base64String, $folder)
    {
        // Check if string contains base64 prefix
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
            $base64String = substr($base64String, strpos($base64String, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif

            $base64String = str_replace(' ', '+', $base64String);
            $imageData = base64_decode($base64String);

            if ($imageData === false) {
                throw new \Exception('Base64 decode failed');
            }

            $fileName = uniqid() . '.' . $type;
            $filePath = $folder . '/' . $fileName;

            \Storage::disk('public')->put($filePath, $imageData);

            return $filePath;
        }

        throw new \Exception('Invalid base64 image format');
    }
}