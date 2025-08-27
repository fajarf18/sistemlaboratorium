<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Peminjaman') }}
        </h2>
    </x-slot>

    <div x-data="{ 
            showPreviewModal: false,
            showDeleteModal: false,
            previewItem: {},
            itemToDelete: '', 
            deleteAction: ''
         }" 
         @keydown.escape.window="showPreviewModal = false; showDeleteModal = false">
        
        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p class="font-bold">Sukses</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p class="font-bold">Gagal</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white p-6 rounded-xl shadow-sm">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                <a href="{{ route('admin.history.download', request()->query()) }}" class="flex items-center justify-center gap-2 w-full md:w-auto px-4 py-2 border border-gray-300 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-50">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    Download
                </a>
                <form action="{{ route('admin.history.index') }}" method="GET" class="relative w-full md:w-1/3">
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
                            <th class="p-4 font-semibold">Tanggal Kembali</th>
                            <th class="p-4 font-semibold text-center">Status Pengembalian</th>
                            <th class="p-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($histories as $history)
                            <tr>
                                <td class="p-4 text-gray-800 font-medium">{{ $history->user->nama }}</td>
                                <td class="p-4 text-gray-500">{{ $history->user->nim }}</td>
                                <td class="p-4 text-gray-500">{{ \Carbon\Carbon::parse($history->tanggal_pinjam)->format('d M Y') }}</td>
                                <td class="p-4 text-gray-500">{{ \Carbon\Carbon::parse($history->tanggal_kembali)->format('d M Y') }}</td>
                                <td class="p-4 text-center">
                                    <span class="inline-block px-3 py-1 text-xs font-medium rounded-full 
                                        @if(optional($history->history)->status_pengembalian == 'Aman') bg-green-100 text-green-800
                                        @elseif(Str::contains(optional($history->history)->status_pengembalian, 'Hilang')) bg-red-100 text-red-800
                                        @elseif(Str::contains(optional($history->history)->status_pengembalian, 'Terlambat')) bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ optional($history->history)->status_pengembalian ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <div class="flex gap-2">
                                        <button @click="showPreviewModal = true; previewItem = {{ json_encode($history) }}" class="p-2 bg-yellow-400 text-white rounded-md hover:bg-yellow-500" title="Lihat Detail">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639l4.43-4.43a1.012 1.012 0 011.43 0l4.43 4.43a1.012 1.012 0 010 1.43l-4.43 4.43a1.012 1.012 0 01-1.43 0l-4.43-4.43zM12.036 12.322a1.012 1.012 0 010-.639l4.43-4.43a1.012 1.012 0 011.43 0l4.43 4.43a1.012 1.012 0 010 1.43l-4.43 4.43a1.012 1.012 0 01-1.43 0l-4.43-4.43z" /></svg>
                                        </button>
                                        <button @click="showDeleteModal = true; itemToDelete = 'peminjaman oleh ' + '{{ $history->user->nama }}'; deleteAction = '{{ route('admin.history.destroy', $history->id) }}'" class="p-2 bg-red-500 text-white rounded-md hover:bg-red-600" title="Hapus Riwayat">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-4 text-center text-gray-500">
                                @if ($search)
                                    Tidak ada riwayat yang cocok dengan pencarian Anda.
                                @else
                                    Tidak ada riwayat peminjaman yang selesai.
                                @endif
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $histories->links() }}</div>
        </div>

        {{-- Modal Preview --}}
        <div x-show="showPreviewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" style="display: none;">
            <div @click.away="showPreviewModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center pb-3 border-b sticky top-0 bg-white">
                    <h3 class="text-lg font-semibold">Detail Riwayat</h3>
                    <button @click="showPreviewModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                
                {{-- KONTEN MODAL DIPERBARUI DI SINI --}}
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
                            <p class="text-gray-500">Tanggal Kembali</p>
                            <p class="font-semibold" x-text="new Date(previewItem.tanggal_kembali).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })"></p>
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
                                            <td class="px-4 py-2 text-center text-gray-600" x-text="(item.jumlah + item.jumlah_hilang) + ' pcs'"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Barang Hilang (hanya tampil jika ada) --}}
                    <template x-if="previewItem.detail_peminjamans.some(item => item.jumlah_hilang > 0)">
                         <div>
                            <h4 class="font-semibold text-md mb-2 text-red-600">Barang Hilang</h4>
                            <div class="border border-red-200 rounded-lg overflow-hidden">
                                <table class="w-full text-sm">
                                    <thead class="bg-red-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left font-semibold text-red-700">Nama Barang</th>
                                            <th class="px-4 py-2 text-center font-semibold text-red-700">Jumlah Hilang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="item in previewItem.detail_peminjamans.filter(i => i.jumlah_hilang > 0)" :key="item.id">
                                            <tr class="border-t border-red-200">
                                                <td class="px-4 py-2 text-red-800" x-text="item.barang.nama_barang"></td>
                                                <td class="px-4 py-2 text-center text-red-800" x-text="item.jumlah_hilang + ' pcs'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                         </div>
                    </template>
                    
                    {{-- Deskripsi Kehilangan (hanya tampil jika ada) --}}
                    <template x-if="previewItem.history && previewItem.history.deskripsi_kehilangan">
                        <div>
                            <h4 class="font-semibold text-md mb-1">Alasan Kehilangan:</h4>
                            <p class="text-sm p-3 bg-gray-50 border rounded-md" x-text="previewItem.history.deskripsi_kehilangan"></p>
                        </div>
                    </template>
                    <template x-if="previewItem.history && previewItem.history.gambar_bukti">
                        <div>
                            <h4 class="font-semibold text-md mb-2">Bukti Pengembalian</h4>
                            <div class="flex justify-center">
                                <img :src="'/storage/' + previewItem.history.gambar_bukti" alt="Bukti Pengembalian" class="w-full max-w-sm h-auto object-cover rounded-lg border">
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Modal Konfirmasi Hapus --}}
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div @click.away="showDeleteModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 mx-4">
                <div class="flex flex-col items-center text-center">
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="h-8 w-8 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                    </div>
                    <h3 class="text-lg font-semibold mt-4">Hapus Riwayat</h3>
                    <p class="text-gray-600 mt-2">Apakah Anda yakin ingin menghapus riwayat <strong x-text="itemToDelete"></strong>? Tindakan ini akan menghapus data secara permanen.</p>
                </div>
                <form :action="deleteAction" method="POST" class="mt-6 flex justify-center gap-4">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="showDeleteModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>