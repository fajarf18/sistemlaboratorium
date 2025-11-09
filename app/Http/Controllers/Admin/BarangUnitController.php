<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangUnit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarangUnitController extends Controller
{
    /**
     * Menampilkan semua units dari suatu barang
     */
    public function index(Barang $barang, Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = $barang->units();

        // Filter berdasarkan status jika ada
        if ($status) {
            if ($status === 'rusak') {
                // 'rusak' adalah istilah lama, terjemahkan menjadi kedua tipe rusak
                $query->whereIn('status', ['rusak_ringan', 'rusak_berat']);
            } elseif ($status === 'hilang') {
                // status hilang sudah dihapus -> tidak ada hasil
                $query->whereRaw('0=1');
            } else {
                $query->where('status', $status);
            }
        }

        // Filter berdasarkan pencarian unit code
        if ($search) {
            $query->where('unit_code', 'like', '%' . $search . '%');
        }

        $units = $query->latest()->paginate(20)->withQueryString();

        return view('admin.barang.units.index', [
            'barang' => $barang,
            'units' => $units,
            'search' => $search,
            'status' => $status,
        ]);
    }

    /**
     * Tambah unit barang baru
     */
    public function store(Request $request, Barang $barang)
    {
        $request->validate([
            'jumlah_unit' => 'required|integer|min:1|max:100',
        ], [
            'jumlah_unit.required' => 'Jumlah unit harus diisi.',
            'jumlah_unit.integer' => 'Jumlah unit harus berupa angka.',
            'jumlah_unit.min' => 'Jumlah unit minimal 1.',
            'jumlah_unit.max' => 'Jumlah unit maksimal 100 sekaligus.',
        ]);

        // Hitung unit terakhir untuk melanjutkan nomor urut
        $lastUnit = $barang->units()->orderBy('id', 'desc')->first();

        if ($lastUnit) {
            // Ambil nomor terakhir dari kode unit
            $lastNumber = (int) substr($lastUnit->unit_code, strrpos($lastUnit->unit_code, '-') + 1);
            $startNumber = $lastNumber + 1;
        } else {
            $startNumber = 1;
        }

        // Buat unit baru
        $createdUnits = [];
        for ($i = 0; $i < $request->jumlah_unit; $i++) {
            $unitNumber = $startNumber + $i;
            $unit = BarangUnit::create([
                'barang_id' => $barang->id,
                'unit_code' => $barang->kode_barang . '-' . str_pad($unitNumber, 3, '0', STR_PAD_LEFT),
                'status' => 'baik',
                'keterangan' => null,
            ]);
            $createdUnits[] = $unit->unit_code;
        }

        // Update stok di tabel barang
        $barang->increment('stok', $request->jumlah_unit);

        return redirect()->back()->with('success', 'Berhasil menambahkan ' . $request->jumlah_unit . ' unit barang baru.');
    }

    /**
     * Update status unit
     */
    public function update(Request $request, BarangUnit $unit)
    {
        $request->validate([
            // Terima nilai lama untuk kompatibilitas, tapi akan di-map ke nilai baru
            'status' => 'required|in:baik,rusak,rusak_ringan,rusak_berat,hilang,dipinjam',
            'keterangan' => 'nullable|string',
        ]);

        // Map nilai legacy ke nilai baru
        $newStatus = $request->status;
        if ($newStatus === 'rusak') {
            $newStatus = 'rusak_ringan';
        }
        if ($newStatus === 'hilang') {
            // 'hilang' dihapus; map ke 'rusak_berat' agar tidak hilang data
            $newStatus = 'rusak_berat';
        }

        $unit->update([
            'status' => $newStatus,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->back()->with('success', 'Status unit berhasil diperbarui.');
    }

    /**
     * Hapus unit barang
     */
    public function destroy(BarangUnit $unit)
    {
        $barang = $unit->barang;

        // Cek apakah unit sedang dipinjam
        if ($unit->status == 'dipinjam') {
            return redirect()->back()->with('error', 'Tidak dapat menghapus unit yang sedang dipinjam.');
        }

        $unit->delete();

        // Update stok barang
        $barang->decrement('stok', 1);

        return redirect()->back()->with('success', 'Unit berhasil dihapus.');
    }
}
