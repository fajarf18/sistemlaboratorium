<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang; // 1. Import model Barang
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin.
     */
    public function index(): View
    {
        return view('admin.dashboard');
    }
}
