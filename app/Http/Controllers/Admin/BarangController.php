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
     * Menampilkan halaman list semua barang dengan fitur pencarian.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $tipe = $request->input('tipe'); // Ambil input filter tipe
        $query = Barang::query();

        // Terapkan filter pencarian jika ada
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', '%' . $search . '%')
                  ->orWhere('kode_barang', 'like', '%' . $search . '%');
            });
        }

        // Terapkan filter tipe jika ada dan bukan "semua"
        if ($tipe) {
            $query->where('tipe', $tipe);
        }

        $barangs = $query->latest()->paginate(10)->withQueryString();

        return view('admin.barang.index', [
            'barangs' => $barangs,
            'search' => $search,
            'tipe' => $tipe, // Kirim nilai filter ke view
        ]);
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

        // Membuat Kode Barang Otomatis
        $lastBarang = Barang::orderBy('id', 'desc')->first();
        if ($lastBarang) {
            $lastNumber = (int) substr($lastBarang->kode_barang, 1);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        $newKodeBarang = 'A' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);

        $path = null;
        if ($request->hasFile('gambar')) {
            // Cara yang lebih baik: Sebutkan folder dan disk secara eksplisit.
            // Ini akan menyimpan file di 'storage/app/public/barang'
            // dan mengembalikan path 'barang/namafile.jpg'
            $path = $request->file('gambar')->store('barang', 'public');
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
            // Hapus gambar lama jika ada
            if ($barang->gambar) {
                Storage::disk('public')->delete($barang->gambar);
            }
            // Simpan gambar baru
            $path = $request->file('gambar')->store('barang', 'public');
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

    /**
     * Menghapus barang dari database.
     */
    public function destroy(Barang $barang)
    {
        // Hapus gambar dari storage jika ada
        if ($barang->gambar) {
            Storage::disk('public')->delete($barang->gambar);
        }
        $barang->delete();
        return redirect()->route('admin.barang.index')->with('success', 'Barang berhasil dihapus.');
    }
    /**
     * Men-download data barang sebagai file CSV.
     */
    public function download()
    {
        $barangs = Barang::all();
        $fileName = "data-barang-" . date('Y-m-d') . ".csv";

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('ID Barang', 'Nama Barang', 'Tipe', 'Stok', 'Deskripsi', 'Tanggal Dibuat');

        $callback = function() use($barangs, $columns) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns, ';');

            foreach ($barangs as $barang) {
                $idBarang = '="' . $barang->kode_barang . '"';
                $tanggalDibuat = '="' . $barang->created_at->format('d/m/Y') . '"';

                fputcsv($file, [
                    $idBarang,
                    $barang->nama_barang,
                    $barang->tipe,
                    $barang->stok,
                    $barang->deskripsi,
                    $tanggalDibuat
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
