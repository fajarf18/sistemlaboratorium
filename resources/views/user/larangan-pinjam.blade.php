@extends('layouts.app')

@section('header', 'Pinjam Barang')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-lg text-center">
    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-yellow-100 text-yellow-500">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
    </div>
    <h3 class="text-2xl font-bold text-gray-800 mb-2">Tidak Bisa Meminjam Barang</h3>
    <p class="text-gray-600 mb-6">
        Anda masih memiliki peminjaman yang sedang aktif. <br>
        Silakan kembalikan barang yang sedang Anda pinjam terlebih dahulu sebelum dapat meminjam barang baru.
    </p>
    <a href="{{ route('user.peminjaman.rincian') }}" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
        Lihat Rincian Pinjaman
    </a>
</div>
@endsection