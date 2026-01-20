<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\KelasPraktikum;
use App\Models\KelasPraktikumItem;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KelasPraktikumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $kelasPraktikums = KelasPraktikum::where('created_by', Auth::id())
            ->with('creator', 'items.barang')
            ->latest()
            ->get();
        
        return view('dosen.kelas-praktikum.index', compact('kelasPraktikums'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $moduls = \App\Models\Modul::where('user_id', Auth::id())->where('is_active', true)->get();
        return view('dosen.kelas-praktikum.create', compact('moduls'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_kuliah' => 'required|string|max:255',
            'nama_kelas' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'modul_id' => 'required|exists:moduls,id',
            'tanggal_praktikum' => 'nullable|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $kelasPraktikum = KelasPraktikum::create([
                'mata_kuliah' => $validated['mata_kuliah'],
                'nama_kelas' => $validated['nama_kelas'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'modul_id' => $validated['modul_id'],
                'tanggal_praktikum' => $validated['tanggal_praktikum'] ?? null,
                'jam_mulai' => $validated['jam_mulai'],
                'jam_selesai' => $validated['jam_selesai'],
                'is_active' => $request->has('is_active') ? true : false,
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('dosen.kelas-praktikum.index')->with('success', 'Kelas praktikum berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan kelas praktikum: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $kelasPraktikum = KelasPraktikum::with('creator', 'modul.items.barang', 'mahasiswa')
            ->where('created_by', Auth::id())
            ->findOrFail($id);
        
        return view('dosen.kelas-praktikum.show', compact('kelasPraktikum'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $kelasPraktikum = KelasPraktikum::with('items')
            ->where('created_by', Auth::id())
            ->findOrFail($id);
        
        $moduls = \App\Models\Modul::where('user_id', Auth::id())->where('is_active', true)->get();
        return view('dosen.kelas-praktikum.edit', compact('kelasPraktikum', 'moduls'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kelasPraktikum = KelasPraktikum::where('created_by', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'mata_kuliah' => 'required|string|max:255',
            'nama_kelas' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'modul_id' => 'required|exists:moduls,id',
            'tanggal_praktikum' => 'nullable|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $kelasPraktikum->update([
                'mata_kuliah' => $validated['mata_kuliah'],
                'nama_kelas' => $validated['nama_kelas'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'modul_id' => $validated['modul_id'],
                'tanggal_praktikum' => $validated['tanggal_praktikum'] ?? null,
                'jam_mulai' => $validated['jam_mulai'],
                'jam_selesai' => $validated['jam_selesai'],
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            // No items to update in KelasPraktikum anymore (items are in Modul)
            // If we had legacy items, we might want to delete them?
            // KelasPraktikumItem::where('kelas_praktikum_id', $kelasPraktikum->id)->delete(); 
            // Better to clean up legacy items if they exist to avoid confusion.
            KelasPraktikumItem::where('kelas_praktikum_id', $kelasPraktikum->id)->delete();

            DB::commit();
            return redirect()->route('dosen.kelas-praktikum.index')->with('success', 'Kelas praktikum berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengupdate kelas praktikum: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $kelasPraktikum = KelasPraktikum::where('created_by', Auth::id())->findOrFail($id);
            $kelasPraktikum->delete();
            return redirect()->route('dosen.kelas-praktikum.index')->with('success', 'Kelas praktikum berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus kelas praktikum: ' . $e->getMessage());
        }
    }
}
