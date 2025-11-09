<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangUnit;
use App\Models\Keranjang;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\PeminjamanUnit;
use App\Models\DosenPengampu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewOrderNotification;

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

        // Ambil dosen pengampu yang aktif
        $dosens = DosenPengampu::where('is_active', true)->get();

        return view('user.keranjang', compact('keranjangItems', 'dosens'));
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

        // Cek stok yang available (hanya yang baik)
        $stokTersedia = $barang->stok_baik;

        if ($request->jumlah > $stokTersedia) {
            return back()->with('error', 'Jumlah peminjaman melebihi stok barang yang tersedia. Stok baik: ' . $stokTersedia);
        }

        $keranjangItem = Keranjang::where('user_id', $userId)
                                ->where('barang_id', $request->barang_id)
                                ->first();

        if ($keranjangItem) {
            $keranjangItem->jumlah += $request->jumlah;
            if ($keranjangItem->jumlah > $stokTersedia) {
                $keranjangItem->jumlah = $stokTersedia;
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
        
        $stokBaikTersedia = $keranjangItem->barang->stok_baik;
        if ($request->jumlah > $stokBaikTersedia) {
            return back()->with('error', 'Jumlah melebihi stok tersedia (baik).');
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
            'dosen_pengampu_id' => 'required|exists:dosen_pengampus,id',
        ], [
            'dosen_pengampu_id.required' => 'Silakan pilih dosen pengampu terlebih dahulu.',
            'dosen_pengampu_id.exists' => 'Dosen pengampu yang dipilih tidak valid.',
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
            // Validasi stok baik tersedia untuk semua item
            foreach ($itemsInCart as $item) {
                $stokBaikTersedia = $item->barang->stok_baik;
                if ($item->jumlah > $stokBaikTersedia) {
                    throw new \Exception('Stok baik untuk barang "' . $item->barang->nama_barang . '" tidak mencukupi. Tersedia: ' . $stokBaikTersedia);
                }
            }

            $peminjaman = Peminjaman::create([
                'user_id' => Auth::id(),
                'dosen_pengampu_id' => $request->dosen_pengampu_id,
                'tanggal_pinjam' => now(),
                'tanggal_wajib_kembali' => now()->addDays(3),
                'status' => 'Menunggu Konfirmasi',
            ]);

            foreach ($itemsInCart as $item) {
                $detailPeminjaman = DetailPeminjaman::create([
                    'peminjaman_id' => $peminjaman->id,
                    'barang_id' => $item->barang_id,
                    'jumlah' => $item->jumlah,
                ]);

                // Assign unit-unit baik yang tersedia
                $availableUnits = BarangUnit::where('barang_id', $item->barang_id)
                    ->where('status', 'baik')
                    ->limit($item->jumlah)
                    ->get();

                foreach ($availableUnits as $unit) {
                    PeminjamanUnit::create([
                        'detail_peminjaman_id' => $detailPeminjaman->id,
                        'barang_unit_id' => $unit->id,
                        'status_pengembalian' => 'belum',
                    ]);
                }

                // Mengurangi stok barang (total stok)
                $item->barang->decrement('stok', $item->jumlah);

                // Hapus item dari keranjang
                $item->delete();
            }

            DB::commit();

            // Kirim notifikasi email setelah transaksi database berhasil
            try {
                $peminjamanLengkap = Peminjaman::with('user', 'detailPeminjaman.barang')->find($peminjaman->id);
                if ($peminjamanLengkap) {
                    Mail::to(config('app.admin_email'))->send(new NewOrderNotification($peminjamanLengkap));
                }
            } catch (\Exception $e) {
                \Log::error('Gagal mengirim email notifikasi checkout: '. $e->getMessage());
            }
            
            return redirect()->route('user.keranjang.index')->with('success', 'Barang berhasil dipinjam, tunggu konfirmasi dari admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Checkout gagal: ' . $e->getMessage());
        }
    }
}
