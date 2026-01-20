<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Modul;
use App\Models\ModulItem;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ModulController extends Controller
{
    public function index()
    {
        $moduls = Modul::where('user_id', Auth::id())->latest()->get();
        return view('dosen.modul.index', compact('moduls'));
    }

    public function create()
    {
        // Assuming we need Barangs to add to module
        $barangs = Barang::all(); 
        return view('dosen.modul.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_modul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        $modul = Modul::create([
            'nama_modul' => $request->nama_modul,
            'deskripsi' => $request->deskripsi,
            'kode_modul' => Str::upper(Str::random(10)),
            'user_id' => Auth::id(),
            'is_active' => true,
        ]);

        foreach ($request->items as $item) {
            $barang = Barang::find($item['barang_id']);
            if ($barang) {
                ModulItem::create([
                    'kode_modul' => $modul->kode_modul,
                    'kode_barang' => $barang->kode_barang,
                    'jumlah' => $item['jumlah'],
                ]);
            }
        }

        return redirect()->route('dosen.modul.index')->with('success', 'Modul berhasil dibuat.');
    }

    public function show(Modul $modul)
    {
        if ($modul->user_id !== Auth::id()) {
            abort(403);
        }
        $modul->load('items.barang');
        return view('dosen.modul.show', compact('modul'));
    }

    public function edit(Modul $modul)
    {
        if ($modul->user_id !== Auth::id()) {
            abort(403);
        }
        $barangs = Barang::all();
        $modul->load('items');
        return view('dosen.modul.edit', compact('modul', 'barangs'));
    }

    public function update(Request $request, Modul $modul)
    {
        if ($modul->user_id !== Auth::id()) {
            abort(403);
        }
        
        $request->validate([
            'nama_modul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        $modul->update($request->only('nama_modul', 'deskripsi'));

        $modul->items()->delete();

        foreach ($request->items as $item) {
            $barang = Barang::find($item['barang_id']);
            if ($barang) {
                ModulItem::create([
                    'kode_modul' => $modul->kode_modul,
                    'kode_barang' => $barang->kode_barang,
                    'jumlah' => $item['jumlah'],
                ]);
            }
        }

        return redirect()->route('dosen.modul.index')->with('success', 'Modul berhasil diperbarui.');
    }

    public function destroy(Modul $modul)
    {
        if ($modul->user_id !== Auth::id()) {
            abort(403);
        }
        $modul->delete();
        return redirect()->route('dosen.modul.index')->with('success', 'Modul berhasil dihapus.');
    }
}
