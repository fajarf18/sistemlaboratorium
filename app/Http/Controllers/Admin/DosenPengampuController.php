<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DosenPengampu;
use Illuminate\Http\Request;

class DosenPengampuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dosens = DosenPengampu::latest()->get();
        return view('admin.dosen-pengampu.index', compact('dosens'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|max:255|unique:dosen_pengampus,nip',
            'email' => 'required|email|max:255|unique:dosen_pengampus,email',
            'no_telp' => 'nullable|string|max:20',
            'mata_kuliah' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        try {
            DosenPengampu::create($validated);
            return redirect()->route('admin.dosen-pengampu.index')
                ->with('success', 'Dosen pengampu berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan dosen pengampu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DosenPengampu $dosenPengampu)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|max:255|unique:dosen_pengampus,nip,' . $dosenPengampu->id,
            'email' => 'required|email|max:255|unique:dosen_pengampus,email,' . $dosenPengampu->id,
            'no_telp' => 'nullable|string|max:20',
            'mata_kuliah' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        try {
            $dosenPengampu->update($validated);
            return redirect()->route('admin.dosen-pengampu.index')
                ->with('success', 'Dosen pengampu berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengupdate dosen pengampu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DosenPengampu $dosenPengampu)
    {
        try {
            $dosenPengampu->delete();
            return redirect()->route('admin.dosen-pengampu.index')
                ->with('success', 'Dosen pengampu berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus dosen pengampu: ' . $e->getMessage());
        }
    }
}
