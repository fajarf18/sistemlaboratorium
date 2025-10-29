<?php

namespace App\Http\Controllers;

use App\Models\Modul;
use App\Models\Keranjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ModulController extends Controller
{
    /**
     * Menampilkan daftar modul
     */
    public function index()
    {
        $moduls = Modul::with('items.barang')
            ->where('is_active', true)
            ->latest()
            ->get();

        return view('user.modul.index', compact('moduls'));
    }

    /**
     * Menambahkan semua alat dalam modul ke keranjang
     */
    public function addToCart($id)
    {
        $modul = Modul::with('items.barang')->findOrFail($id);

        if (!$modul->is_active) {
            return back()->with('error', 'Modul ini tidak aktif');
        }

        DB::beginTransaction();
        try {
            $userId = Auth::id();
            $addedItems = 0;
            $errors = [];

            foreach ($modul->items as $item) {
                $barang = $item->barang;
                $jumlah = $item->jumlah;

                // Cek stok yang tersedia
                $stokTersedia = $barang->stok_baik;

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
                    $keranjangItem->jumlah = $newJumlah;
                    $keranjangItem->save();
                } else {
                    Keranjang::create([
                        'user_id' => $userId,
                        'barang_id' => $barang->id,
                        'jumlah' => $jumlah,
                    ]);
                }

                $addedItems++;
            }

            DB::commit();

            if ($addedItems > 0) {
                $message = "Berhasil menambahkan {$addedItems} alat dari modul ke keranjang";
                if (!empty($errors)) {
                    $message .= ". Beberapa alat tidak dapat ditambahkan: " . implode(', ', $errors);
                }
                return redirect()->route('user.keranjang.index')->with('success', $message);
            } else {
                return back()->with('error', 'Tidak ada alat yang dapat ditambahkan: ' . implode(', ', $errors));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
