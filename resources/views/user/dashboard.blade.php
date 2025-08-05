@extends('layouts.app')

@section('header', 'Hello ' . auth()->user()->nama . ' 👋')

@section('content')
<div class="space-y-6">
    
    {{-- Kartu Aksi Cepat --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('user.pinjam.index') }}" class="flex flex-col items-center justify-center rounded-xl bg-white p-6 text-center shadow-sm transition hover:bg-sky-50 hover:shadow-md">
            <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-sky-100 text-sky-500">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15m0-3l-3-3m0 0l-3 3m3-3v11.25" /></svg>
            </div>
            <p class="font-semibold text-gray-700">Pinjam Barang</p>
        </a>
        <a href="{{ route('user.kembalikan.index') }}" class="flex flex-col items-center justify-center rounded-xl bg-white p-6 text-center shadow-sm transition hover:bg-sky-50 hover:shadow-md">
            <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-sky-100 text-sky-500">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg>
            </div>
            <p class="font-semibold text-gray-700">Kembalikan Barang</p>
        </a>
         <a href="{{ route('user.history.index') }}" class="flex flex-col items-center justify-center rounded-xl bg-white p-6 text-center shadow-sm transition hover:bg-sky-50 hover:shadow-md">
            <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-sky-100 text-sky-500">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
            </div>
            <p class="font-semibold text-gray-700">History Peminjaman</p>
        </a>
         <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center rounded-xl bg-white p-6 text-center shadow-sm transition hover:bg-sky-50 hover:shadow-md">
            <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-sky-100 text-sky-500">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            </div>
            <p class="font-semibold text-gray-700">Profil</p>
        </a>
    </div>

    {{-- Banner Peringatan Dinamis --}}
    @if($hasPendingConfirmation)
        <div class="rounded-lg border-2 border-yellow-300 bg-yellow-100 p-4 text-center font-medium text-yellow-800">
            Silahkan menuju TU Lab untuk konfirmasi pengambilan barang dalam waktu 1x24 jam.
        </div>
    @elseif($nearestDueDate)
        <div class="rounded-lg border-2 border-red-300 bg-red-100 p-4 text-center font-medium text-red-800">
            Kembalikan Barang Sebelum Tanggal {{ \Carbon\Carbon::parse($nearestDueDate)->format('d/m/Y') }} Agar Tidak Terkena Denda.
        </div>
    @endif

    {{-- Daftar Barang yang Sedang Dipinjam --}}
    <div class="rounded-xl bg-white p-4 sm:p-6 shadow-sm">
        <div class="mb-4 flex flex-col items-start justify-between sm:flex-row sm:items-center">
            <h3 class="text-lg font-semibold text-gray-800">Barang yang Sedang Dipinjam</h3>
            <a href="{{ route('user.peminjaman.rincian') }}" class="mt-2 text-sm font-medium text-blue-600 hover:underline sm:mt-0">View All</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full min-w-[600px] text-left text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="p-4 font-medium">Item Name</th>
                        <th class="p-4 font-medium">Amount</th>
                        <th class="p-4 font-medium">Tanggal Pinjam</th>
                        <th class="p-4 font-medium">Wajib Kembali</th>
                        <th class="p-4 font-medium text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($activePeminjamans as $peminjaman)
                        @foreach($peminjaman->detailPeminjamans as $detail)
                        <tr>
                            <td class="p-4 text-gray-700">{{ $detail->barang->nama_barang }}</td>
                            <td class="p-4 text-gray-500">{{ $detail->jumlah }} pcs</td>
                            <td class="p-4 text-gray-500">{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }}</td>
                            <td class="p-4 text-gray-500">{{ \Carbon\Carbon::parse($peminjaman->tanggal_wajib_kembali)->format('d/m/Y') }}</td>
                            {{-- PERUBAHAN DI SINI: Ditambahkan 'text-center' --}}
                            <td class="p-4 text-center">
                                <span class="inline-block px-3 py-1 text-xs font-medium rounded-full 
                                    @if($peminjaman->status == 'Menunggu Konfirmasi') bg-yellow-100 text-yellow-800
                                    @elseif($peminjaman->status == 'Dipinjam') bg-green-100 text-green-800
                                    @endif">
                                    {{ $peminjaman->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-500">
                                Tidak ada barang yang sedang dipinjam.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection