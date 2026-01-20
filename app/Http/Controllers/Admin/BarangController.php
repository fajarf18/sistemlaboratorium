<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangUnit;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Exports\BarangsExport;
use App\Exports\BarangsTemplateExport;
use App\Imports\BarangsImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class BarangController extends Controller
{
    /**
     * Menampilkan halaman list semua barang dengan fitur pencarian.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $tipe = $request->input('tipe'); // Ambil input filter tipe
        $query = Barang::withCount('units');

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
            'kode_barang' => 'required|string|max:255|unique:barangs,kode_barang',
            'tipe' => 'required|in:Habis Pakai,Tidak Habis Pakai',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'deskripsi' => 'nullable|string',
        ]);

        $kodeBarang = $request->kode_barang;

        $path = null;
        if ($request->hasFile('gambar')) {
            // Cara yang lebih baik: Sebutkan folder dan disk secara eksplisit.
            // Ini akan menyimpan file di 'storage/app/public/barang'
            // dan mengembalikan path 'barang/namafile.jpg'
            $path = $request->file('gambar')->store('barang', 'public');
        }

        DB::transaction(function () use ($request, $kodeBarang, $path) {
            // Buat barang
            $barang = Barang::create([
                'nama_barang' => $request->nama_barang,
                'kode_barang' => $kodeBarang,
                'tipe' => $request->tipe,
                'stok' => $request->stok,
                'gambar' => $path,
                'deskripsi' => $request->deskripsi,
            ]);

            // Auto-generate unit barang sesuai stok
            if ($request->stok > 0) {
                for ($i = 1; $i <= $request->stok; $i++) {
                    BarangUnit::create([
                        'barang_id' => $barang->id,
                        'unit_code' => $kodeBarang . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                        'status' => 'baik',
                        'keterangan' => null,
                    ]);
                }
            }
        });

        return redirect()->route('admin.barang.index')->with('success', 'Barang berhasil ditambahkan dengan ' . $request->stok . ' unit.');
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

        DB::transaction(function () use ($request, $barang, $path) {
            $oldStok = $barang->stok;
            $newStok = $request->stok;
            $stokDiff = $newStok - $oldStok;

            // Update data barang
            $barang->update([
                'nama_barang' => $request->nama_barang,
                'kode_barang' => $request->kode_barang,
                'tipe' => $request->tipe,
                'stok' => $request->stok,
                'gambar' => $path,
                'deskripsi' => $request->deskripsi,
            ]);

            // Handle perubahan stok
            if ($stokDiff > 0) {
                // Stok bertambah - buat unit baru
                $existingUnitsCount = $barang->units()->count();
                for ($i = 1; $i <= $stokDiff; $i++) {
                    $unitNumber = $existingUnitsCount + $i;
                    BarangUnit::create([
                        'barang_id' => $barang->id,
                        'unit_code' => $request->kode_barang . '-' . str_pad($unitNumber, 3, '0', STR_PAD_LEFT),
                        'status' => 'baik',
                        'keterangan' => null,
                    ]);
                }
            } elseif ($stokDiff < 0) {
                // Stok berkurang - hapus unit dengan status 'baik' yang tidak sedang dipinjam
                $unitsToDelete = abs($stokDiff);
                $availableUnits = $barang->units()
                    ->where('status', 'baik')
                    ->limit($unitsToDelete)
                    ->get();

                if ($availableUnits->count() < $unitsToDelete) {
                    throw new \Exception('Tidak dapat mengurangi stok. Hanya ada ' . $availableUnits->count() . ' unit dengan status baik yang tersedia.');
                }

                foreach ($availableUnits as $unit) {
                    $unit->delete();
                }
            }
        });

        return redirect()->route('admin.barang.index')->with('success', 'Barang berhasil diperbarui.');
    }

    /**
     * Menghapus barang dari database.
     */
    public function destroy(Barang $barang)
    {
        // Larang hapus jika ada peminjaman aktif yang melibatkan barang ini
        $hasActiveLoan = Peminjaman::whereIn('status', ['Menunggu Konfirmasi', 'Dipinjam', 'Tunggu Konfirmasi Admin'])
            ->whereHas('detailPeminjaman', function ($q) use ($barang) {
                $q->where('barang_id', $barang->id);
            })
            ->exists();

        if ($hasActiveLoan) {
            return back()->with('error', 'Barang tidak dapat dihapus karena terdapat peminjaman aktif pada barang ini.');
        }

        // Larang hapus jika ada unit yang masih berstatus dipinjam
        $hasBorrowedUnit = $barang->units()->where('status', 'dipinjam')->exists();
        if ($hasBorrowedUnit) {
            return back()->with('error', 'Barang tidak dapat dihapus karena ada unit yang sedang dipinjam.');
        }

        DB::transaction(function () use ($barang) {
            // Hapus gambar dari storage jika ada
            if ($barang->gambar) {
                Storage::disk('public')->delete($barang->gambar);
            }

            // Hapus semua unit barang terkait
            $barang->units()->delete();

            // Hapus barang
            $barang->delete();
        });

        return redirect()->route('admin.barang.index')->with('success', 'Barang dan semua unitnya berhasil dihapus.');
    }
    /**
     * Men-download data barang sebagai file Excel.
     */
    public function download()
    {
        $fileName = "Data-Barang-" . date('Y-m-d-His') . ".xlsx";
        return Excel::download(new BarangsExport, $fileName);
    }

    /**
     * Men-download template import Excel.
     */
    public function downloadTemplate()
    {
        $fileName = "Template-Import-Barang.xlsx";
        return Excel::download(new BarangsTemplateExport, $fileName);
    }

    /**
     * Import data barang dari Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ], [
            'file.required' => 'File Excel wajib dipilih.',
            'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls).',
            'file.max' => 'Ukuran file maksimal 2MB.',
        ]);

        try {
            Excel::import(new BarangsImport, $request->file('file'));

            return redirect()->route('admin.barang.index')->with('success', 'Data barang berhasil diimport dari Excel.');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errors = [];

            foreach ($failures as $failure) {
                $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return redirect()->route('admin.barang.index')
                ->with('error', 'Gagal import data')
                ->with('import_errors', $errors);
        } catch (\Exception $e) {
            return redirect()->route('admin.barang.index')->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }
}
