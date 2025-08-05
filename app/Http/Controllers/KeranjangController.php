<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Keranjang;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KeranjangController extends Controller
{
    /**
     * Menampilkan halaman keranjang.
     */
    public function index()
    {
        $keranjangItems = Keranjang::with('barang')
            ->where('user_id', Auth::id())
            ->get();
            
        return view('user.keranjang', compact('keranjangItems'));
    }

    /**
     * Menambahkan barang ke keranjang.
     */
    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jumlah' => 'required|integer|min:1',
        ]);

        $barang = Barang::findOrFail($request->barang_id);
        $userId = Auth::id();

        if ($request->jumlah > $barang->stok) {
            return back()->with('error', 'Jumlah peminjaman melebihi stok yang tersedia.');
        }

        $keranjangItem = Keranjang::where('user_id', $userId)
                                ->where('barang_id', $request->barang_id)
                                ->first();

        if ($keranjangItem) {
            $keranjangItem->jumlah += $request->jumlah;
            if ($keranjangItem->jumlah > $barang->stok) {
                $keranjangItem->jumlah = $barang->stok;
            }
            $keranjangItem->save();
        } else {
            Keranjang::create([
                'user_id' => $userId,
                'barang_id' => $request->barang_id,
                'jumlah' => $request->jumlah,
            ]);
        }

        return back()->with('success', 'Barang berhasil ditambahkan ke keranjang!');
    }

    /**
     * Update jumlah barang di keranjang.
     */
    public function update(Request $request, $id)
    {
        $request->validate(['jumlah' => 'required|integer|min:1']);
        $keranjangItem = Keranjang::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        if ($request->jumlah > $keranjangItem->barang->stok) {
            return back()->with('error', 'Jumlah melebihi stok.');
        }

        $keranjangItem->update(['jumlah' => $request->jumlah]);
        return back()->with('success', 'Jumlah barang berhasil diperbarui.');
    }

    /**
     * Menghapus barang dari keranjang.
     */
    public function destroy($id)
    {
        $keranjangItem = Keranjang::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $keranjangItem->delete();
        return back()->with('success', 'Barang berhasil dihapus dari keranjang.');
    }

    /**
     * Memproses checkout, memindahkan barang ke peminjaman, dan mengurangi stok.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|json',
        ]);

        $selectedItemIds = json_decode($request->items);

        if (empty($selectedItemIds)) {
            return back()->with('error', 'Tidak ada barang yang dipilih untuk checkout.');
        }

        $itemsInCart = Keranjang::with('barang')
            ->where('user_id', Auth::id())
            ->whereIn('id', $selectedItemIds)
            ->get();

        DB::beginTransaction();
        try {
            foreach ($itemsInCart as $item) {
                if ($item->jumlah > $item->barang->stok) {
                    throw new \Exception('Stok untuk barang "' . $item->barang->nama_barang . '" tidak mencukupi.');
                }
            }

            $peminjaman = Peminjaman::create([
                'user_id' => Auth::id(),
                'tanggal_pinjam' => now(),
                'tanggal_wajib_kembali' => now()->addDays(3),
                'status' => 'Menunggu Konfirmasi',
            ]);

            foreach ($itemsInCart as $item) {
                DetailPeminjaman::create([
                    'peminjaman_id' => $peminjaman->id,
                    'barang_id' => $item->barang_id,
                    'jumlah' => $item->jumlah,
                ]);

                // Mengurangi stok barang
                $item->barang->stok -= $item->jumlah;
                $item->barang->save();

                // Hapus item dari keranjang
                $item->delete();
            }

            DB::commit();
            return redirect()->route('user.keranjang.index')->with('checkout_success', true);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Checkout gagal: ' . $e->getMessage());
        }
    }
}