<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Auth;

class CheckUserBorrowingStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (Auth::check()) {
            // Cek apakah user memiliki peminjaman dengan status "Dipinjam" atau "Tunggu Konfirmasi Admin"
            $hasActiveBorrowing = Peminjaman::where('user_id', Auth::id())
                ->whereIn('status', ['Dipinjam', 'Tunggu Konfirmasi Admin'])
                ->exists();

            if ($hasActiveBorrowing) {
                return redirect()->route('user.dashboard')
                    ->with('error', 'Anda tidak dapat meminjam barang atau mengakses modul praktikum karena masih memiliki peminjaman aktif. Silakan tunggu konfirmasi admin untuk pengembalian barang Anda.');
            }
        }

        return $next($request);
    }
}
