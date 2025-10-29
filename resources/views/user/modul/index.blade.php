@extends('layouts.app')

@section('header', 'Modul Praktikum')

@section('content')

<div class="bg-white p-4 sm:p-6 rounded-xl shadow-lg">
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div class="text-lg font-semibold text-gray-700">
            Daftar Modul Praktikum
        </div>
        <a href="{{ route('user.keranjang.index') }}" class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition relative">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c.51 0 .962-.344 1.084-.845l1.956-7.146A.75.75 0 0020.25 6h-15.75" /></svg>
            <span>Keranjang</span>
            @if(auth()->user()->keranjangs->count() > 0)
            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ auth()->user()->keranjangs->count() }}</span>
            @endif
        </a>
    </div>

    @if($moduls->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($moduls as $modul)
                <div class="border border-gray-200 rounded-lg p-5 hover:shadow-lg transition">
                    <div class="mb-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $modul->nama_modul }}</h3>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $modul->kode_modul }}</span>
                        </div>
                        @if($modul->deskripsi)
                            <p class="text-sm text-gray-600 mb-3">{{ $modul->deskripsi }}</p>
                        @endif
                    </div>

                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Daftar Alat ({{ $modul->items->count() }} item):</h4>
                        <ul class="space-y-1 max-h-40 overflow-y-auto">
                            @foreach($modul->items as $item)
                                <li class="text-sm text-gray-600 flex justify-between items-center">
                                    <span>{{ $item->barang->nama_barang }}</span>
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $item->jumlah }}x</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <form action="{{ route('user.modul.addToCart', $modul->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tambah ke Keranjang
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <p class="mt-2 text-sm text-gray-500">Belum ada modul praktikum yang tersedia</p>
        </div>
    @endif
</div>
@endsection
