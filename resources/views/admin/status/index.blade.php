<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Status Peminjam Aktif') }}
        </h2>
    </x-slot>

    <div x-data="{ 
            showPreviewModal: false,
            previewItem: {}
         }" 
         @keydown.escape.window="showPreviewModal = false">
        
        <div class="bg-white p-6 rounded-xl shadow-sm">
            <div class="flex flex-col md:flex-row justify-end items-center gap-4 mb-6">
                <form action="{{ route('admin.status.index') }}" method="GET" class="relative w-full md:w-1/3">
                    <input type="text" name="search" placeholder="Cari Nama atau NIM..." value="{{ $search ?? '' }}" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <button type="submit" class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    </button>
                </form>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="p-4 font-semibold">Nama Peminjam</th>
                            <th class="p-4 font-semibold">NIM</th>
                            <th class="p-4 font-semibold">Tanggal Pinjam</th>
                            <th class="p-4 font-semibold">Wajib Kembali</th>
                            <th class="p-4 font-semibold text-center">Status</th>
                            <th class="p-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($activePeminjamans as $peminjaman)
                            <tr>
                                <td class="p-4 text-gray-800 font-medium">{{ $peminjaman->user->nama }}</td>
                                <td class="p-4 text-gray-500">{{ $peminjaman->user->nim }}</td>
                                <td class="p-4 text-gray-500">{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d M Y') }}</td>
                                <td class="p-4 text-gray-500">{{ \Carbon\Carbon::parse($peminjaman->tanggal_wajib_kembali)->format('d M Y') }}</td>
                                <td class="p-4 text-center">
                                    <span class="inline-block px-3 py-1 text-xs font-medium rounded-full 
                                        @if($peminjaman->status == 'Dipinjam') bg-green-100 text-green-800
                                        @elseif($peminjaman->status == 'Menunggu Konfirmasi') bg-yellow-100 text-yellow-800
                                        @elseif($peminjaman->status == 'Tunggu Konfirmasi Admin') bg-orange-100 text-orange-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $peminjaman->status }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <div class="flex gap-2">
                                        <button @click="showPreviewModal = true; previewItem = {{ json_encode($peminjaman) }}" class="p-2 bg-yellow-400 text-white rounded-md hover:bg-yellow-500" title="Lihat Detail">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639l4.43-4.43a1.012 1.012 0 011.43 0l4.43 4.43a1.012 1.012 0 010 1.43l-4.43 4.43a1.012 1.012 0 01-1.43 0l-4.43-4.43zM12.036 12.322a1.012 1.012 0 010-.639l4.43-4.43a1.012 1.012 0 011.43 0l4.43 4.43a1.012 1.012 0 010 1.43l-4.43 4.43a1.012 1.012 0 01-1.43 0l-4.43-4.43z" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-4 text-center text-gray-500">
                                @if ($search)
                                    Tidak ada peminjam aktif yang cocok dengan pencarian Anda.
                                @else
                                    Tidak ada peminjam yang sedang aktif.
                                @endif
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $activePeminjamans->links() }}</div>
        </div>

        {{-- Modal Preview --}}
        <div x-show="showPreviewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" style="display: none;">
            <div @click.away="showPreviewModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center pb-3 border-b sticky top-0 bg-white">
                    <h3 class="text-lg font-semibold">Detail Peminjaman Aktif</h3>
                    <button @click="showPreviewModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                
                <div x-show="previewItem.user" class="mt-4 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Nama Peminjam</p>
                            <p class="font-semibold" x-text="previewItem.user.nama"></p>
                        </div>
                        <div>
                            <p class="text-gray-500">NIM</p>
                            <p class="font-semibold" x-text="previewItem.user.nim"></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Tanggal Pinjam</p>
                            <p class="font-semibold" x-text="new Date(previewItem.tanggal_pinjam).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })"></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Wajib Kembali</p>
                            <p class="font-semibold" x-text="new Date(previewItem.tanggal_wajib_kembali).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })"></p>
                        </div>
                    </div>

                    {{-- Rincian Barang --}}
                    <div>
                        <h4 class="font-semibold text-md mb-2">Rincian Barang</h4>
                        <div class="border rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Nama Barang</th>
                                        <th class="px-4 py-2 text-center font-semibold text-gray-600">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="item in previewItem.detail_peminjamans" :key="item.id">
                                        <tr class="border-t">
                                            <td class="px-4 py-2 text-gray-800" x-text="item.barang.nama_barang"></td>
                                            <td class="px-4 py-2 text-center text-gray-600" x-text="item.jumlah + ' pcs'"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>