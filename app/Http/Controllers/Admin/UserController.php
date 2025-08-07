<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = User::where('role', 'user');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nim', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.user.index', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Validasi data yang masuk
        $request->validate([
            'nama' => 'required|string|max:255',
            'nim' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'semester' => 'required|integer|min:1',
            'prodi' => 'required|string|max:255',
        ]);

        // Update data pengguna
        $user->update([
            'nama' => $request->nama,
            'nim' => $request->nim,
            'semester' => $request->semester,
            'prodi' => $request->prodi,
        ]);

        // Redirect kembali dengan pesan sukses
        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }
}
