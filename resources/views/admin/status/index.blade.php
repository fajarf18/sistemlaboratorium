<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Status Peminjam Aktif') }}
        </h2>
    </x-slot>

    <div x-data="{
            showDetailModal: false,
            detailItem: {},
            isUnitsModalOpen: false,
            selectedItem: null,

            openDetailModal(peminjaman) {
                this.detailItem = peminjaman;
                this.showDetailModal = true;
            },

            openUnitsModal(item) {
                this.selectedItem = item;
                this.isUnitsModalOpen = true;
            },

            closeDetailModal() {
                this.showDetailModal = false;
                this.detailItem = {};
            }
         }"
         @keydown.escape.window="showDetailModal = false; isUnitsModalOpen = false">

        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
                <p class="font-bold">Sukses</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
                <p class="font-bold">Gagal</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white p-6 rounded-xl shadow-sm">
            <!-- Search Form -->
            <div class="flex flex-col md:flex-row justify-end items-center gap-4 mb-6">
                <form action="{{ route('admin.status.index') }}" method="GET" class="relative w-full md:w-1/3">
                    <input type="text" name="search" placeholder="Cari Nama atau NIM..." value="{{ $search ?? '' }}" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <button type="submit" class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    </button>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="p-4 font-semibold">Nama Peminjam</th>
                            <th class="p-4 font-semibold">NIM</th>
                            <th class="p-4 font-semibold">Dosen Pengampu</th>
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
                                <td class="p-4 text-gray-500">
                                    @if($peminjaman->dosenPengampu)
                                        <div class="font-medium text-gray-900">{{ $peminjaman->dosenPengampu->nama }}</div>
                                        <div class="text-xs text-gray-500">{{ $peminjaman->dosenPengampu->mata_kuliah ?? '-' }}</div>
                                    @else
                                        <span class="text-gray-400 italic">-</span>
                                    @endif
                                </td>
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
                                        <!-- Tombol Lihat Detail -->
                                        <button @click="openDetailModal({{ json_encode($peminjaman) }})" class="p-2 bg-blue-500 text-white rounded-md hover:bg-blue-600" title="Lihat Detail">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        </button>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-4 text-center text-gray-500">
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

            <!-- Pagination -->
            <div class="mt-6">{{ $activePeminjamans->links() }}</div>
        </div>

        {{-- Modal Detail Peminjaman --}}
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" style="display: none;">
            <div @click.away="closeDetailModal()" class="bg-white rounded-xl shadow-xl w-full max-w-2xl p-6 mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center pb-3 border-b sticky top-0 bg-white">
                    <h3 class="text-lg font-semibold">Detail Peminjaman Aktif</h3>
                    <button @click="closeDetailModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>

                <div x-show="detailItem.user" class="mt-4 space-y-4">
                    <!-- Info Peminjam -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Nama Peminjam</p>
                            <p class="font-semibold" x-text="detailItem.user?.nama"></p>
                        </div>
                        <div>
                            <p class="text-gray-500">NIM</p>
                            <p class="font-semibold" x-text="detailItem.user?.nim"></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Dosen Pengampu</p>
                            <p class="font-semibold" x-text="detailItem.dosen_pengampu?.nama || '-'"></p>
                            <p class="text-xs text-gray-500" x-text="detailItem.dosen_pengampu?.mata_kuliah || ''"></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Status</p>
                            <span class="inline-block px-3 py-1 text-xs font-medium rounded-full mt-1"
                                :class="{
                                    'bg-green-100 text-green-800': detailItem.status === 'Dipinjam',
                                    'bg-yellow-100 text-yellow-800': detailItem.status === 'Menunggu Konfirmasi',
                                    'bg-orange-100 text-orange-800': detailItem.status === 'Tunggu Konfirmasi Admin'
                                }"
                                x-text="detailItem.status">
                            </span>
                        </div>
                        <div>
                            <p class="text-gray-500">Tanggal Pinjam</p>
                            <p class="font-semibold" x-text="detailItem.tanggal_pinjam ? new Date(detailItem.tanggal_pinjam).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'"></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Wajib Kembali</p>
                            <p class="font-semibold" x-text="detailItem.tanggal_wajib_kembali ? new Date(detailItem.tanggal_wajib_kembali).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'"></p>
                        </div>
                    </div>

                    {{-- Rincian Barang & Unit --}}
                    <div>
                        <h4 class="font-semibold text-md mb-2">Rincian Barang & Unit yang Dipinjam</h4>
                        <div class="border rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Nama Barang</th>
                                        <th class="px-4 py-2 text-center font-semibold text-gray-600">Jumlah Unit</th>
                                        <th class="px-4 py-2 text-center font-semibold text-gray-600">Detail Units</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="item in detailItem.detail_peminjamans" :key="item.id">
                                        <tr class="border-t">
                                            <td class="px-4 py-2 text-gray-800" x-text="item.barang?.nama_barang"></td>
                                            <td class="px-4 py-2 text-center text-gray-600">
                                                <span class="font-semibold" x-text="item.peminjaman_units?.length || 0"></span> unit
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <button @click="openUnitsModal(item)" class="px-3 py-1 text-xs font-medium text-white bg-purple-500 rounded hover:bg-purple-600">
                                                    Lihat Units
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button @click="closeDetailModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        {{-- Modal Nested untuk Detail Units --}}
        <div x-show="isUnitsModalOpen" @keydown.escape.window="isUnitsModalOpen = false" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 text-center">
                <div @click="isUnitsModalOpen = false" x-show="isUnitsModalOpen" class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-60"></div>

                <div x-show="isUnitsModalOpen" class="inline-block w-full max-w-3xl p-6 my-8 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">
                            Detail Units - <span x-text="selectedItem?.barang?.nama_barang"></span>
                        </h2>
                        <button @click="isUnitsModalOpen = false" class="text-gray-600 hover:text-gray-800">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="mb-4 p-3 bg-gray-50 rounded">
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <p class="text-gray-600">Total Unit Dipinjam</p>
                                <p class="text-lg font-bold text-blue-600" x-text="selectedItem?.peminjaman_units?.length || 0"></p>
                            </div>
                            <div>
                                <p class="text-gray-600">Kode Barang</p>
                                <p class="text-lg font-bold text-gray-800" x-text="selectedItem?.barang?.kode_barang || '-'"></p>
                            </div>
                        </div>
                    </div>

                    <div class="border rounded-lg overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold">No</th>
                                    <th class="px-4 py-2 text-left font-semibold">Kode Unit</th>
                                    <th class="px-4 py-2 text-center font-semibold">Status Unit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <template x-for="(unit, index) in selectedItem?.peminjaman_units" :key="unit.id">
                                    <tr>
                                        <td class="px-4 py-3" x-text="index + 1"></td>
                                        <td class="px-4 py-3 font-mono text-xs font-semibold" x-text="unit.barang_unit?.unit_code || '-'"></td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full font-semibold"
                                                  :class="{
                                                      'bg-blue-100 text-blue-700': unit.barang_unit?.status === 'dipinjam',
                                                      'bg-green-100 text-green-700': unit.barang_unit?.status === 'baik',
                                                      'bg-yellow-100 text-yellow-700': unit.barang_unit?.status === 'rusak',
                                                      'bg-red-100 text-red-700': unit.barang_unit?.status === 'hilang'
                                                  }"
                                                  x-text="unit.barang_unit?.status ? unit.barang_unit.status.charAt(0).toUpperCase() + unit.barang_unit.status.slice(1) : '-'">
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button @click="isUnitsModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
