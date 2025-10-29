<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Modul;
use App\Models\ModulItem;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModulController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $moduls = Modul::with('items.barang')->latest()->get();
        return view('admin.modul.index', compact('moduls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barangs = Barang::all();
        return view('admin.modul.create', compact('barangs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_modul' => 'required|string|max:255',
            'kode_modul' => 'required|string|max:255|unique:moduls,kode_modul',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
            'barang_ids' => 'required|array',
            'barang_ids.*' => 'exists:barangs,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1'
        ]);

        DB::beginTransaction();
        try {
            $modul = Modul::create([
                'nama_modul' => $validated['nama_modul'],
                'kode_modul' => $validated['kode_modul'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'is_active' => $request->has('is_active') ? true : false
            ]);

            foreach ($validated['barang_ids'] as $index => $barang_id) {
                ModulItem::create([
                    'modul_id' => $modul->id,
                    'barang_id' => $barang_id,
                    'jumlah' => $validated['jumlah'][$index]
                ]);
            }

            DB::commit();
            return redirect()->route('admin.modul.index')->with('success', 'Modul berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan modul: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $modul = Modul::with('items.barang')->findOrFail($id);
        return view('admin.modul.show', compact('modul'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $modul = Modul::with('items')->findOrFail($id);
        $barangs = Barang::all();
        return view('admin.modul.edit', compact('modul', 'barangs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $modul = Modul::findOrFail($id);

        $validated = $request->validate([
            'nama_modul' => 'required|string|max:255',
            'kode_modul' => 'required|string|max:255|unique:moduls,kode_modul,' . $id,
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
            'barang_ids' => 'required|array',
            'barang_ids.*' => 'exists:barangs,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1'
        ]);

        DB::beginTransaction();
        try {
            $modul->update([
                'nama_modul' => $validated['nama_modul'],
                'kode_modul' => $validated['kode_modul'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'is_active' => $request->has('is_active') ? true : false
            ]);

            // Hapus item lama
            ModulItem::where('modul_id', $modul->id)->delete();

            // Tambah item baru
            foreach ($validated['barang_ids'] as $index => $barang_id) {
                ModulItem::create([
                    'modul_id' => $modul->id,
                    'barang_id' => $barang_id,
                    'jumlah' => $validated['jumlah'][$index]
                ]);
            }

            DB::commit();
            return redirect()->route('admin.modul.index')->with('success', 'Modul berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengupdate modul: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $modul = Modul::findOrFail($id);
            $modul->delete();
            return redirect()->route('admin.modul.index')->with('success', 'Modul berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus modul: ' . $e->getMessage());
        }
    }
}
