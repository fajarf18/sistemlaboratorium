@extends('layouts.app')

@section('header', 'Kelas Praktikum')

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
            Daftar Kelas Praktikum
        </div>
        <a href="{{ route('user.keranjang.index') }}" class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition relative">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c.51 0 .962-.344 1.084-.845l1.956-7.146A.75.75 0 0020.25 6h-15.75" /></svg>
            <span>Keranjang</span>
            @if(auth()->user()->keranjangs->count() > 0)
            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ auth()->user()->keranjangs->count() }}</span>
            @endif
        </a>
    </div>

    @if($kelasPraktikums->count() > 0)
        @if($userJoinedKelas)
            <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="font-semibold">Anda sudah join kelas:</span> {{ $userJoinedKelas->nama_kelas }}
                        <p class="text-sm mt-1">Silakan batalkan join kelas tersebut terlebih dahulu sebelum join kelas lain.</p>
                    </div>
                    <form action="{{ route('user.kelas-praktikum.leave', $userJoinedKelas->id) }}" method="POST" class="ml-4">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                            Batal Join
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($kelasPraktikums as $kelas)
                @php
                    $isJoined = $kelas->mahasiswa->contains(auth()->id());
                    $canJoin = !$userJoinedKelas || ($userJoinedKelas && $userJoinedKelas->id == $kelas->id);
                @endphp
                <div class="border border-gray-200 rounded-lg p-5 hover:shadow-lg transition {{ $isJoined ? 'border-sky-500 bg-sky-50' : '' }}">
                    <div class="mb-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $kelas->mata_kuliah }}</h3>
                            <div class="flex gap-2">
                                @if($isJoined)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Sudah Join
                                    </span>
                                @endif
                                @if($kelas->tanggal_praktikum)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-sky-100 text-sky-800 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ \Carbon\Carbon::parse($kelas->tanggal_praktikum)->format('d/m/Y') }}
                                    </span>
                                @endif
                                @if($kelas->modul->jam_mulai && $kelas->modul->jam_selesai)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ \Carbon\Carbon::parse($kelas->modul->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($kelas->modul->jam_selesai)->format('H:i') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-1">
                            <span class="font-medium">Kelas:</span> {{ $kelas->nama_kelas }}
                        </p>
                        <p class="text-sm text-gray-600 mb-1">
                            <span class="font-medium">Dosen:</span> {{ $kelas->creator->nama }}
                        </p>
                        <p class="text-sm text-gray-600 mb-3">
                            <span class="font-medium">Modul:</span> <span class="text-blue-600 font-medium">{{ $kelas->modul->nama_modul }}</span>
                        </p>
                        @if($kelas->deskripsi)
                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($kelas->deskripsi, 100) }}</p>
                        @endif
                    </div>

                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Daftar Alat ({{ $kelas->modul->items->count() }} item):</h4>
                        <ul class="space-y-1 max-h-40 overflow-y-auto">
                            @foreach($kelas->modul->items->take(5) as $item)
                                <li class="text-sm text-gray-600 flex justify-between items-center">
                                    <span>{{ $item->barang->nama_barang }}</span>
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $item->jumlah }}x</span>
                                </li>
                            @endforeach
                            @if($kelas->modul->items->count() > 5)
                                <li class="text-sm text-gray-500 italic">... dan {{ $kelas->modul->items->count() - 5 }} alat lainnya</li>
                            @endif
                        </ul>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('user.kelas-praktikum.show', $kelas->id) }}" 
                           class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-center">
                            Detail
                        </a>
                        @if($isJoined)
                            <form action="{{ route('user.kelas-praktikum.leave', $kelas->id) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center justify-center gap-2">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Batal Join
                                </button>
                            </form>
                        @else
                            <form action="{{ route('user.kelas-praktikum.join', $kelas->id) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" 
                                        {{ !$canJoin ? 'disabled' : '' }}
                                        class="w-full px-4 py-2 {{ $canJoin ? 'bg-sky-600 hover:bg-sky-700' : 'bg-gray-400 cursor-not-allowed' }} text-white rounded-lg transition flex items-center justify-center gap-2"
                                        {{ !$canJoin ? 'title="Anda sudah join kelas lain"' : '' }}>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Join Kelas
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
            </svg>
            <p class="mt-2 text-sm text-gray-500">Belum ada kelas praktikum yang tersedia</p>
        </div>
    @endif
</div>
@endsection
