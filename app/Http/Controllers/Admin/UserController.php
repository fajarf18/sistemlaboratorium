<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; // Import Hash facade
use Illuminate\Validation\Rules;      // Import Rules facade
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Menampilkan halaman list semua pengguna dengan fitur pencarian dan filter.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $prodi = $request->input('prodi');
        $semester = $request->input('semester');

        $query = User::query()->where('role', 'user');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nim', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        if ($prodi) $query->where('prodi', $prodi);
        if ($semester) $query->where('semester', $semester);

        $users = $query->latest()->paginate(10)->withQueryString();

        $prodis = User::where('role', 'user')->whereNotNull('prodi')->distinct()->pluck('prodi');
        $semesters = User::where('role', 'user')->whereNotNull('semester')->distinct()->orderBy('semester', 'asc')->pluck('semester');

        return view('admin.user.index', compact('users', 'search', 'prodi', 'semester', 'prodis', 'semesters'));
    }

    /**
     * Memperbarui data pengguna.
     */
    public function update(Request $request, User $user)
    {
        // Validasi input, termasuk email dan password (opsional)
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nomor_wa' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'prodi' => ['required', 'string', 'max:255'],
            'semester' => ['required', 'integer', 'min:1'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()], // Password opsional, tapi jika diisi harus terkonfirmasi
        ]);

        // Update data utama pengguna
        $user->update($request->only('nama', 'nim', 'email', 'nomor_wa', 'prodi', 'semester'));

        // Jika ada input password baru, update passwordnya
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Menghapus pengguna.
     */
   public function destroy(User $user)
    {
        // Pastikan kita tidak menghapus admin
        if ($user->role === 'admin') {
            return back()->with('error', 'Admin tidak dapat dihapus.');
        }

        // ================= LOGIKA PENGHAPUSAN BERANTAI =================
        try {
            DB::transaction(function () use ($user) {
                // 1. Muat semua relasi peminjaman milik pengguna
                $peminjamans = $user->peminjamans()->with(['detailPeminjaman', 'history'])->get();

                foreach ($peminjamans as $peminjaman) {
                    // 2. Hapus semua detail peminjaman terkait
                    $peminjaman->detailPeminjaman()->delete();
                    
                    // 3. Hapus history peminjaman terkait (jika ada)
                    if ($peminjaman->history) {
                        $peminjaman->history()->delete();
                    }
                }

                // 4. Setelah relasi anaknya dihapus, hapus semua peminjaman
                $user->peminjamans()->delete();

                // 5. Terakhir, hapus pengguna itu sendiri
                $user->delete();
            });

            return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');

        } catch (\Exception $e) {
            // Jika terjadi error, batalkan semua operasi dan tampilkan pesan
            return back()->with('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
        }
        // ===============================================================
    }

    /**
     * Men-download data pengguna sebagai file CSV.
     */
    public function download(Request $request)
    {
        $search = $request->input('search');
        $prodi = $request->input('prodi');
        $semester = $request->input('semester');

        $query = User::query()->where('role', 'user');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nim', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('nomor_wa', 'like', '%' . $search . '%');
            });
        }
        if ($prodi) $query->where('prodi', $prodi);
        if ($semester) $query->where('semester', $semester);

        $users = $query->get();
        $fileName = "data-pengguna-" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['NIM', 'Nama', 'Email', 'Nomor WA', 'Prodi', 'Semester', 'Tanggal Daftar'];

        $callback = function() use($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->nim,
                    $user->nama,
                    $user->email,
                    $user->nomor_wa ?? '-',
                    $user->prodi,
                    $user->semester,
                    $user->created_at->format('d/m/Y')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}