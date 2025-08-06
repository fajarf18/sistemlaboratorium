<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BarangController extends Controller
{
    /**
     * Menampilkan halaman list semua barang.
     */
    public function index(): View
    {
        $barangs = Barang::latest()->get();
        return view('admin.barang.index', ['barangs' => $barangs]);
    }

    /**
     * Menyimpan barang baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'tipe' => 'required|in:Habis Pakai,Tidak Habis Pakai',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'deskripsi' => 'nullable|string',
        ]);

        $lastBarang = Barang::orderBy('id', 'desc')->first();
        if ($lastBarang) {
            $lastNumber = (int) substr($lastBarang->kode_barang, 1);
            $newNumber = $lastNumber + 1;
        } else { $newNumber = 1; }
        $newKodeBarang = 'A' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);

        $path = null;
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('public/barang');
            $path = str_replace('public/', '', $path);
        }

        Barang::create([
            'nama_barang' => $request->nama_barang,
            'kode_barang' => $newKodeBarang,
            'tipe' => $request->tipe,
            'stok' => $request->stok,
            'gambar' => $path,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    /**
     * Menghapus barang dari database.
     */
    public function destroy(Barang $barang)
    {
        if ($barang->gambar) { Storage::delete('public/' . $barang->gambar); }
        $barang->delete();
        return redirect()->route('admin.barang.index')->with('success', 'Barang berhasil dihapus.');
    }

    /**
     * Memperbarui data barang di database.
     */
    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => ['required', 'string', 'max:255', Rule::unique('barangs')->ignore($barang->id)],
            'tipe' => 'required|in:Habis Pakai,Tidak Habis Pakai',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'deskripsi' => 'nullable|string',
        ]);

        $path = $barang->gambar;
        if ($request->hasFile('gambar')) {
            if ($barang->gambar) { Storage::delete('public/' . $barang->gambar); }
            $path = $request->file('gambar')->store('public/barang');
            $path = str_replace('public/', '', $path);
        }

        $barang->update([
            'nama_barang' => $request->nama_barang,
            'kode_barang' => $request->kode_barang,
            'tipe' => $request->tipe,
            'stok' => $request->stok,
            'gambar' => $path,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.barang.index')->with('success', 'Barang berhasil diperbarui.');
    }
}
