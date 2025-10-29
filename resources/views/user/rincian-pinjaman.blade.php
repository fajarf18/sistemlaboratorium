@extends('layouts.app')

@section('header', 'Rincian Pinjaman')

@section('content')
<div class="bg-white p-4 sm:p-6 rounded-xl shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-800">Tabel Rincian</h3>
        
        {{-- Tombol Dinamis --}}
        @if ($canReturn)
            {{-- Jika BISA mengembalikan, tombol aktif dan mengarah ke halaman pengembalian --}}
            <a href="{{ route('user.kembalikan.index') }}" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                Kembalikan Barang
            </a>
        @else
            {{-- Jika TIDAK BISA, tombol nonaktif --}}
            <button disabled class="px-4 py-2 bg-gray-400 text-white font-semibold rounded-lg cursor-not-allowed">
                Kembalikan Barang
            </button>
        @endif
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Item Name</th>
                    <th scope="col" class="px-6 py-3">Amount</th>
                    <th scope="col" class="px-6 py-3">Dosen Pengampu</th>
                    <th scope="col" class="px-6 py-3">Tanggal Pinjam</th>
                    <th scope="col" class="px-6 py-3">Wajib Kembali</th>
                    <th scope="col" class="px-6 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($peminjamans as $peminjaman)
                    @foreach ($peminjaman->detailPeminjamans as $detail)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap align-middle">
                            {{ $detail->barang->nama_barang }}
                        </th>
                        <td class="px-6 py-4 align-middle">{{ $detail->jumlah }} pcs</td>
                        <td class="px-6 py-4 align-middle">
                            @if($peminjaman->dosenPengampu)
                                <div class="font-medium text-gray-900">{{ $peminjaman->dosenPengampu->nama }}</div>
                                <div class="text-xs text-gray-500">{{ $peminjaman->dosenPengampu->mata_kuliah ?? '-' }}</div>
                            @else
                                <span class="text-gray-400 italic">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-middle">{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 align-middle">
                            @php
                                $wajibKembali = \Carbon\Carbon::parse($peminjaman->tanggal_wajib_kembali);
                                $isOverdue = !in_array($peminjaman->status, ['Dikembalikan']) && $wajibKembali->isPast();
                            @endphp
                            <span class="{{ $isOverdue ? 'text-red-500 font-bold' : '' }}">
                                {{ $wajibKembali->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center align-middle">
                            @php
                                $statusClass = '';
                                if ($peminjaman->status == 'Menunggu Konfirmasi') {
                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                } elseif ($peminjaman->status == 'Dipinjam') {
                                    $statusClass = 'bg-green-100 text-green-800';
                                } elseif ($peminjaman->status == 'Tunggu Konfirmasi Admin') {
                                    $statusClass = 'bg-orange-100 text-orange-800';
                                } else {
                                    $statusClass = 'bg-gray-100 text-gray-800';
                                }
                            @endphp
                            <span class="inline-block w-48 text-center px-3 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                {{ $peminjaman->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                @empty
                <tr>
                    <td colspan="6" class="text-center py-10 text-gray-500">Tidak ada pinjaman aktif.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection