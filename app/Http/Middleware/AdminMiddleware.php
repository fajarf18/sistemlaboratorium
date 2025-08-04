<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
// app/Http/Middleware/AdminMiddleware.php
public function handle(Request $request, Closure $next): Response
{
    // Pastikan user sudah login dan rolenya adalah 'admin'
    if (auth()->check() && auth()->user()->role == 'admin') {
        return $next($request);
    }

    // Jika bukan admin, kembalikan ke halaman dashboard biasa atau tampilkan error 403
    return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    // atau abort(403, 'UNAUTHORIZED ACTION.');
}
}
