@extends('layouts.app')

@section('header', 'Keranjang')

@section('content')
{{-- Initialize Alpine.js. Tambahkan checkoutSuccess dari session --}}
<div class="bg-white p-4 sm:p-6 rounded-xl shadow-lg" x-data="keranjangData({{ session('checkout_success') ? 'true' : 'false' }})">
    
    {{-- Notifikasi --}}
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

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="p-4 w-10">
                        <input type="checkbox" @click="toggleAll($el.checked)" class="rounded border-gray-300">
                    </th>
                    <th scope="col" class="px-6 py-3">Tools name</th>
                    <th scope="col" class="px-6 py-3">Image</th>
                    <th scope="col" class="px-6 py-3">Jenis</th>
                    <th scope="col" class="px-6 py-3 text-center">Amount</th>
                    <th scope="col" class="px-6 py-3 text-center">Hapus</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($keranjangItems as $item)
                {{-- Tambahkan class dinamis untuk menandai baris yang stoknya kurang --}}
                <tr class="bg-white border-b hover:bg-gray-50" :class="{ 'opacity-50 bg-red-50': {{ $item->jumlah }} > {{ $item->barang->stok }} }">
                    <td class="p-4">
                        <input type="checkbox" value="{{ $item->id }}" x-model="selectedItems" :disabled="{{ $item->jumlah > $item->barang->stok }}" class="rounded border-gray-300">
                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                        {{ $item->barang->nama_barang }}
                        @if($item->jumlah > $item->barang->stok)
                            <span class="block text-xs text-red-500 font-normal">(Stok tidak mencukupi: {{ $item->barang->stok }})</span>
                        @endif
                    </th>
                    <td class="px-6 py-4">
                        {{-- Pastikan ada fallback jika gambar tidak ada --}}
                        <img src="{{ optional($item->barang)->gambar ? asset('storage/' . $item->barang->gambar) : 'https://placehold.co/100' }}" alt="{{ $item->barang->nama_barang }}" class="w-12 h-12 object-cover rounded">
                    </td>
                    <td class="px-6 py-4">{{ $item->barang->tipe }}</td>
                    <td class="px-6 py-4">
                        {{-- PERBAIKAN ADA DI FORM INI --}}
                        <form action="{{ route('user.keranjang.update', $item->id) }}" method="POST" class="flex items-center justify-center space-x-2">
                            @csrf
                            @method('PATCH')
                            
                            {{-- Tombol Minus: ditambahkan this.form.submit() --}}
                            <button type="button" 
                                    onclick="this.nextElementSibling.stepDown(); this.form.submit();" 
                                    class="px-2 py-1 border border-gray-300 rounded-md text-gray-600 hover:bg-gray-100">-</button>
                            
                            <input type="number" 
                                   name="jumlah" 
                                   value="{{ $item->jumlah }}" 
                                   min="1" 
                                   max="{{ $item->barang->stok }}" 
                                   onchange="this.form.submit()" 
                                   class="w-16 text-center border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            
                            {{-- Tombol Plus: ditambahkan this.form.submit() --}}
                            <button type="button" 
                                    onclick="this.previousElementSibling.stepUp(); this.form.submit();" 
                                    class="px-2 py-1 border border-gray-300 rounded-md text-gray-600 hover:bg-gray-100">+</button>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('user.keranjang.destroy', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-10 text-gray-500">Keranjang Anda kosong.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($keranjangItems->isNotEmpty())
    <div class="flex justify-end mt-6">
        <button @click="showModal = true" :disabled="selectedItems.length === 0" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
            Checkout (<span x-text="selectedItems.length"></span> item)
        </button>
    </div>
    @endif

    <!-- Modal Konfirmasi Checkout -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
        <div @click.outside="showModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-8 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 text-blue-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800">Cek Kembali Barang</h3>
            <p class="text-2xl font-bold text-gray-800 mb-2">Apakah Sudah Sesuai?</p>
            <p class="text-gray-500 text-sm mb-6">Jika Sudah Bisa Klik Sudah, Jika belum Klik Belum</p>
            
            <form action="{{ route('user.checkout.process') }}" method="POST" @submit="isProcessing = true">
            @csrf
            <input type="hidden" name="items" :value="JSON.stringify(selectedItems)">
            <div class="space-y-3">
                     <button type="submit" 
                        :disabled="isProcessing"
                        class="w-full rounded-lg bg-blue-600 text-white py-3 font-semibold hover:bg-blue-700 transition"
                        :class="{'bg-gray-400 cursor-not-allowed': isProcessing, 'hover:bg-blue-700': !isProcessing}">
                    <span x-show="!isProcessing">Sudah</span>
                    <span x-show="isProcessing">Memproses...</span>
                </button>
                    <button type="button" @click="showModal = false" class="w-full rounded-lg bg-white text-gray-700 py-3 font-semibold border border-gray-300 hover:bg-gray-100 transition">Belum</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Berhasil Checkout -->
    <div x-show="checkoutSuccess" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-8 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 text-blue-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Berhasil</h3>
            <p class="text-gray-500 text-sm mb-6">Silahkan Menuju Ke Tata Usaha Laboratorium Untuk Konfirmasi Mengambil Barang (1x24 Jam)</p>
            
            <a href="{{ route('user.peminjaman.rincian') }}" class="w-full block rounded-lg bg-blue-600 text-white py-3 font-semibold hover:bg-blue-700 transition">Rincian Peminjaman</a>
        </div>
    </div>
</div>

<script>
    function keranjangData(checkoutSuccess = false) {
        return {
            showModal: false,
            checkoutSuccess: checkoutSuccess,
            selectedItems: [],
            isProcessing: false,
            toggleAll(checked) {
                let validItems = [];
                if (checked) {
                    @foreach($keranjangItems as $item)
                        @if($item->jumlah <= $item->barang->stok)
                            validItems.push('{{ $item->id }}');
                        @endif
                    @endforeach
                }
                this.selectedItems = validItems;
            }
        }
    }
</script>
@endsection