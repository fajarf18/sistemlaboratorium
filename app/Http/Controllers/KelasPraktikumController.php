<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\KelasPraktikum;
use App\Models\Keranjang;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KelasPraktikumController extends Controller
{
    /**
     * Menampilkan daftar semua kelas aktif yang bisa di-join.
     */
    public function index(): View
    {
        $kelasPraktikums = KelasPraktikum::with('creator', 'modul.items.barang', 'mahasiswa')
            ->where('is_active', true)
            ->latest()
            ->get();
        
        // Cek kelas yang sudah di-join oleh user
        $userJoinedKelas = Auth::user()->kelasPraktikumsJoined()->first();
        
        return view('user.kelas-praktikum.index', compact('kelasPraktikums', 'userJoinedKelas'));
    }

    /**
     * Menampilkan detail kelas praktikum.
     */
    public function show($id): View
    {
        $kelasPraktikum = KelasPraktikum::with('creator', 'modul.items.barang', 'mahasiswa')
            ->where('is_active', true)
            ->findOrFail($id);
        
        // Cek apakah user sudah join kelas ini
        $isJoined = $kelasPraktikum->mahasiswa()->where('user_id', Auth::id())->exists();
        
        // Cek kelas lain yang sudah di-join oleh user
        $userJoinedKelas = Auth::user()->kelasPraktikumsJoined()->where('kelas_praktikums.id', '!=', $id)->first();
        
        return view('user.kelas-praktikum.show', compact('kelasPraktikum', 'isJoined', 'userJoinedKelas'));
    }

    /**
     * Mahasiswa join kelas - barang otomatis masuk keranjang.
     */
    public function joinKelas($id)
    {
        $kelasPraktikum = KelasPraktikum::with('modul.items.barang', 'creator')
            ->where('is_active', true)
            ->findOrFail($id);
        
        $userId = Auth::id();
        
        // Cek apakah user sudah join kelas lain
        $userJoinedKelas = Auth::user()->kelasPraktikumsJoined()->where('kelas_praktikums.id', '!=', $id)->first();
        if ($userJoinedKelas) {
            return back()->with('error', "Anda sudah join kelas '{$userJoinedKelas->nama_kelas}'. Silakan batalkan join kelas tersebut terlebih dahulu sebelum join kelas lain.");
        }
        
        // Cek apakah user sudah join kelas ini
        if ($kelasPraktikum->mahasiswa()->where('user_id', $userId)->exists()) {
            return back()->with('error', 'Anda sudah join kelas ini.');
        }
        
        $addedItems = 0;
        $errors = [];
        
        DB::beginTransaction();
        try {
            foreach ($kelasPraktikum->modul->items as $item) {
                $barang = $item->barang;
                $jumlah = $item->jumlah;
                
                // Cek stok yang tersedia
                $stokTersedia = $barang->stok_pinjam;
                
                if ($jumlah > $stokTersedia) {
                    $errors[] = "{$barang->nama_barang}: stok tidak mencukupi (tersedia: {$stokTersedia})";
                    continue;
                }
                
                // Cek apakah barang sudah ada di keranjang
                $keranjangItem = Keranjang::where('user_id', $userId)
                    ->where('barang_id', $barang->id)
                    ->first();
                
                if ($keranjangItem) {
                    $newJumlah = $keranjangItem->jumlah + $jumlah;
                    if ($newJumlah > $stokTersedia) {
                        $errors[] = "{$barang->nama_barang}: jumlah total melebihi stok (tersedia: {$stokTersedia})";
                        continue;
                    }
                    // Update jika sudah ada, tapi tetap set dari_kelas dan dosen_id
                    // Creator kelas adalah Dosen (User)
                    
                    $keranjangItem->jumlah = $newJumlah;
                    $keranjangItem->kelas_praktikum_id = $kelasPraktikum->id;
                    $keranjangItem->dosen_id = $kelasPraktikum->created_by;
                    $keranjangItem->dari_kelas = true;
                    $keranjangItem->save();
                } else {
                    Keranjang::create([
                        'user_id' => $userId,
                        'barang_id' => $barang->id,
                        'jumlah' => $jumlah,
                        'kelas_praktikum_id' => $kelasPraktikum->id,
                        'dosen_id' => $kelasPraktikum->created_by,
                        'dari_kelas' => true,
                    ]);
                }
                
                $addedItems++;
            }
            
            // Tambahkan mahasiswa ke relasi many-to-many (jika belum ada)
            if (!$kelasPraktikum->mahasiswa()->where('user_id', $userId)->exists()) {
                $kelasPraktikum->mahasiswa()->attach($userId);
            }
            
            DB::commit();
            
            if ($addedItems > 0) {
                $message = "Berhasil menambahkan {$addedItems} alat dari kelas praktikum ke keranjang";
                if (!empty($errors)) {
                    $message .= ". Beberapa alat tidak dapat ditambahkan: " . implode(', ', $errors);
                }
                return redirect()->route('user.keranjang.index')->with('success', $message);
            } else {
                return back()->with('error', 'Tidak ada alat yang dapat ditambahkan: ' . implode(', ', $errors));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal join kelas: ' . $e->getMessage());
        }
    }

    /**
     * Mahasiswa batal join kelas - hapus dari pivot dan hapus item dari keranjang.
     */
    public function leaveKelas($id)
    {
        $kelasPraktikum = KelasPraktikum::with('modul.items.barang')
            ->findOrFail($id);
        
        $userId = Auth::id();
        
        // Cek apakah user sudah join kelas ini
        if (!$kelasPraktikum->mahasiswa()->where('user_id', $userId)->exists()) {
            return back()->with('error', 'Anda belum join kelas ini.');
        }
        
        DB::beginTransaction();
        try {
            // Hapus dari pivot table
            $kelasPraktikum->mahasiswa()->detach($userId);
            
            // Hapus item dari keranjang yang berasal dari kelas ini
            Keranjang::where('user_id', $userId)
                ->where('kelas_praktikum_id', $kelasPraktikum->id)
                ->where('dari_kelas', true)
                ->delete();
            
            DB::commit();
            
            return redirect()->route('user.kelas-praktikum.index')->with('success', 'Berhasil membatalkan join kelas praktikum. Item dari kelas ini telah dihapus dari keranjang.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan join kelas: ' . $e->getMessage());
        }
    }
}
