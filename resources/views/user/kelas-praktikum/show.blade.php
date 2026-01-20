@extends('layouts.app')

@section('header', 'Detail Kelas Praktikum')

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

    <div class="mb-6">
        <a href="{{ route('user.kelas-praktikum.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
            ← Kembali ke Daftar Kelas
        </a>
    </div>

    <div class="space-y-6">
        {{-- Info Kelas --}}
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ $kelasPraktikum->nama_kelas }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-600">Dosen Pengampu</p>
                    <p class="font-medium text-gray-900">{{ $kelasPraktikum->creator->nama }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Mata Kuliah</p>
                    <p class="font-medium text-gray-900">{{ $kelasPraktikum->mata_kuliah }}</p>
                </div>
                @if($kelasPraktikum->tanggal_praktikum)
                <div>
                    <p class="text-sm text-gray-600">Tanggal Praktikum</p>
                    <p class="font-medium text-gray-900">
                        {{ \Carbon\Carbon::parse($kelasPraktikum->tanggal_praktikum)->format('d M Y') }}
                    </p>
                </div>
                @endif
                @if($kelasPraktikum->jam_mulai && $kelasPraktikum->jam_selesai)
                <div>
                    <p class="text-sm text-gray-600">Waktu</p>
                    <p class="font-medium text-gray-900">
                        {{ \Carbon\Carbon::parse($kelasPraktikum->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($kelasPraktikum->jam_selesai)->format('H:i') }}
                    </p>
                </div>
                @endif
            </div>
            @if($kelasPraktikum->deskripsi)
                <div class="mt-4">
                    <p class="text-sm text-gray-600 mb-1">Deskripsi</p>
                    <p class="text-gray-900">{{ $kelasPraktikum->deskripsi }}</p>
                </div>
            @endif
        </div>

        {{-- Informasi Modul --}}
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Informasi Modul Praktikum</h3>
            <div class="bg-sky-50 p-4 rounded-lg mb-4 border border-sky-100">
                <p class="font-medium text-sky-900 text-lg">{{ $kelasPraktikum->modul->nama_modul }}</p>
                <p class="text-sky-700 text-sm mb-2">{{ $kelasPraktikum->modul->kode_modul }}</p>
                <p class="text-gray-600 text-sm">{{ $kelasPraktikum->modul->deskripsi ?: 'Tidak ada deskripsi modul.' }}</p>
            </div>

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Daftar Alat (Otomatis Masuk Keranjang)</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Alat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Tersedia</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($kelasPraktikum->modul->items as $index => $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->barang->nama_barang }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->barang->tipe }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->jumlah }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($item->barang->stok_pinjam >= $item->jumlah)
                                        <span class="text-green-600 font-medium">{{ $item->barang->stok_pinjam }}</span>
                                    @else
                                        <span class="text-red-600 font-bold">{{ $item->barang->stok_pinjam }} (Kurang)</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($kelasPraktikum->modul->items->isEmpty())
                <p class="text-center text-gray-500 py-4 italic">Modul ini belum memiliki daftar alat.</p>
            @endif
        </div>

        {{-- Tombol Join/Batal --}}
        <div class="pt-4 border-t">
            @if($isJoined)
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="font-semibold">Anda sudah join kelas ini.</span>
                </div>
                <form action="{{ route('user.kelas-praktikum.leave', $kelasPraktikum->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center justify-center gap-2 font-semibold">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal Join Kelas
                    </button>
                </form>
                <p class="text-sm text-gray-500 mt-2 text-center">
                    Membatalkan join akan menghapus semua item dari kelas ini dari keranjang Anda.
                </p>
            @elseif($userJoinedKelas)
                <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                    <span class="font-semibold">Anda sudah join kelas:</span> {{ $userJoinedKelas->nama_kelas }}
                    <p class="text-sm mt-1">Silakan batalkan join kelas tersebut terlebih dahulu sebelum join kelas ini.</p>
                </div>
                <form action="{{ route('user.kelas-praktikum.join', $kelasPraktikum->id) }}" method="POST">
                    @csrf
                    <button type="submit" disabled class="w-full px-6 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed transition flex items-center justify-center gap-2 font-semibold">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Join Kelas - Tidak Tersedia
                    </button>
                </form>
            @else
                <form action="{{ route('user.kelas-praktikum.join', $kelasPraktikum->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-6 py-3 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition flex items-center justify-center gap-2 font-semibold">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Join Kelas - Tambahkan Semua Alat ke Keranjang
                    </button>
                </form>
                <p class="text-sm text-gray-500 mt-2 text-center">
                    Semua alat akan ditambahkan ke keranjang dengan jumlah sesuai default. Anda dapat mengubah jumlah sebelum checkout.
                </p>
            @endif
        </div>
    </div>
</div>
@endsection
