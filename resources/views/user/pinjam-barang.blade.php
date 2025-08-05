@extends('layouts.app')

@section('header', 'Pinjam Barang')

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
    
    <form action="{{ route('user.pinjam.index') }}" method="GET">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div class="relative w-full md:w-auto md:flex-1">
                <input type="text" name="search" placeholder="Search Item..." value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
            <div class="flex items-center gap-4 w-full md:w-auto">
                <select name="filter" onchange="this.form.submit()" class="flex-1 md:flex-none border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="semua">Semua Tipe</option>
                    @foreach ($tipeBarang as $tipe)
                        <option value="{{ $tipe->tipe }}" @if(request('filter') == $tipe->tipe) selected @endif>{{ $tipe->tipe }}</option>
                    @endforeach
                </select>

                <a href="{{ route('user.keranjang.index') }}" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition relative">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c.51 0 .962-.344 1.084-.845l1.956-7.146A.75.75 0 0020.25 6h-15.75" /></svg>
                    <span>Keranjang</span>
                    @if(auth()->user()->keranjangs->count() > 0)
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ auth()->user()->keranjangs->count() }}</span>
                    @endif
                </a>
            </div>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Item Name</th>
                    <th scope="col" class="px-6 py-3">Tipe</th>
                    <th scope="col" class="px-6 py-3">Stock</th>
                    <th scope="col" class="px-6 py-3 text-center">Amount</th>
                    <th scope="col" class="px-6 py-3 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($barangs as $barang)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <form action="{{ route('user.keranjang.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="barang_id" value="{{ $barang->id }}">
                        
                        {{-- PERUBAHAN DI SINI: Menambahkan kelas 'align-middle' --}}
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap align-middle">{{ $barang->nama_barang }}</th>
                        <td class="px-6 py-4 align-middle">{{ $barang->tipe }}</td>
                        <td class="px-6 py-4 align-middle">{{ $barang->stok }} pcs</td>
                        <td class="px-6 py-4 align-middle">
                            <div class="flex items-center justify-center space-x-2">
                                <button type="button" onclick="this.nextElementSibling.stepDown()" class="px-2 py-1 border border-gray-300 rounded-md text-gray-600 hover:bg-gray-100" @if($barang->stok == 0) disabled @endif>-</button>
                                <input type="number" name="jumlah" value="1" min="1" max="{{ $barang->stok }}" class="w-16 text-center border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" @if($barang->stok == 0) disabled @endif>
                                <button type="button" onclick="this.previousElementSibling.stepUp()" class="px-2 py-1 border border-gray-300 rounded-md text-gray-600 hover:bg-gray-100" @if($barang->stok == 0) disabled @endif>+</button>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center align-middle">
                            <button type="submit" 
                                    @if($barang->stok == 0) disabled @endif
                                    class="px-4 py-2 text-white rounded-lg transition {{ $barang->stok == 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700' }}">
                                + Tambah
                            </button>
                        </td>
                    </form>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-6">
                        <p class="text-gray-500">Barang tidak ditemukan.</p>
                        <a href="{{ route('user.pinjam.index') }}" class="text-blue-600 hover:underline mt-2 inline-block">Reset Filter</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $barangs->links() }}
    </div>
</div>
@endsection