<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
// ...
public function handle(Request $request, Closure $next): Response
{
    // Ganti '!Auth::user()->is_admin' menjadi pengecekan role
    if (Auth::check() && Auth::user()->role !== 'admin') {
        return $next($request);
    }
    return redirect()->route('admin.dashboard');
}
// ...
}
