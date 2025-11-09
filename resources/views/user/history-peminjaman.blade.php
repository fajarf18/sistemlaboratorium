@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('header', 'History Peminjaman')

@section('content')
<div class="bg-white p-4 sm:p-6 rounded-xl shadow-lg" x-data="historyData({{ session('pengembalian_sukses') ? 'true' : 'false' }})">
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Tanggal Pinjam</th>
                    <th scope="col" class="px-6 py-3">Dosen Pengampu</th>
                    <th scope="col" class="px-6 py-3">Tanggal Kembali</th>
                    <th scope="col" class="px-6 py-3 text-center">Status Peminjaman</th>
                    <th scope="col" class="px-6 py-3 text-center">Status Pengembalian</th>
                    <th scope="col" class="px-6 py-3 text-center">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($historyPeminjamans as $history)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 align-middle">{{ \Carbon\Carbon::parse($history->tanggal_pinjam)->format('d F Y') }}</td>
                    <td class="px-6 py-4 align-middle">
                        @if($history->dosenPengampu)
                            <div class="font-medium text-gray-900">{{ $history->dosenPengampu->nama }}</div>
                            <div class="text-xs text-gray-500">{{ $history->dosenPengampu->mata_kuliah ?? '-' }}</div>
                        @else
                            <span class="text-gray-400 italic">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 align-middle">{{ $history->tanggal_kembali ? \Carbon\Carbon::parse($history->tanggal_kembali)->format('d F Y') : '-' }}</td>
                    <td class="px-6 py-4 text-center align-middle">
                        <span class="inline-block w-40 text-center px-3 py-1 text-xs font-medium rounded-full
                            @if($history->status == 'Menunggu Konfirmasi') bg-purple-100 text-purple-800
                            @elseif($history->status == 'Tunggu Konfirmasi Admin') bg-yellow-100 text-yellow-800
                            @elseif($history->status == 'Dipinjam') bg-green-100 text-green-800
                            @elseif($history->status == 'Dikembalikan') bg-blue-100 text-blue-800
                            @elseif($history->status == 'Ditolak') bg-red-100 text-red-800
                            @endif">
                            {{ $history->status }}
                        </span>
                    </td>
                    @php
                        $statusLabel = $history->status == 'Dikembalikan' ? $history->final_status_pengembalian : null;
                        $statusLower = Str::lower($statusLabel ?? '');
                        $statusClass = 'bg-gray-100 text-gray-800';

                        if (Str::contains($statusLower, 'rusak')) {
                            $statusClass = 'bg-red-100 text-red-800';
                        } elseif (Str::contains($statusLower, 'terlambat')) {
                            $statusClass = 'bg-yellow-100 text-yellow-800';
                        } elseif (Str::contains($statusLower, 'aman')) {
                            $statusClass = 'bg-green-100 text-green-800';
                        }
                    @endphp
                    <td class="px-6 py-4 text-center align-middle">
                        @if($statusLabel)
                            <span class="inline-block px-3 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center align-middle">
                        <button @click="fetchDetail({{ $history->id }})" class="text-blue-600 hover:underline">
                            Detail
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-10 text-gray-500">Belum ada history peminjaman.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div x-show="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-8 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 text-blue-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Berhasil</h3>
            <p class="text-gray-500 text-sm mb-6">Pengajuan pengembalian Anda telah dikirim dan sedang menunggu konfirmasi dari admin.</p>
            <button @click="showSuccessModal = false" class="w-full block rounded-lg bg-blue-600 text-white py-3 font-semibold hover:bg-blue-700 transition">Tutup</button>
        </div>
    </div>

    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" style="display: none;">
        <div @click.outside="showModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-xl font-bold text-gray-800">Detail Peminjaman</h3>
                <button @click="showModal = false" class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
            </div>

            <div x-show="loading" class="text-center py-10">Memuat data...</div>

            <div x-show="!loading && detail" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Tanggal Pinjam</p>
                        <p class="font-semibold" x-text="new Date(detail.tanggal_pinjam).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })"></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Dosen Pengampu</p>
                        <p class="font-semibold" x-text="detail.dosen_pengampu ? detail.dosen_pengampu.nama : '-'"></p>
                        <p class="text-xs text-gray-500" x-text="detail.dosen_pengampu?.mata_kuliah || ''"></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal Kembali</p>
                        <p class="font-semibold" x-text="detail.tanggal_kembali ? new Date(detail.tanggal_kembali).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'"></p>
                    </div>
                </div>

                <template x-if="detail.status === 'Dikembalikan'">
                    <div class="p-3 rounded-lg border-l-4"
                         :class="{
                             'bg-green-50 border-green-500': detail.final_status_pengembalian?.toLowerCase().includes('aman') && !detail.final_status_pengembalian?.toLowerCase().includes('rusak'),
                             'bg-yellow-50 border-yellow-500': detail.final_status_pengembalian?.toLowerCase().includes('rusak') || detail.final_status_pengembalian?.toLowerCase().includes('terlambat'),
                         }">
                        <p class="font-semibold text-sm">Status Pengembalian:</p>
                        <p class="font-bold" x-text="detail.final_status_pengembalian"></p>
                    </div>
                </template>

                <div>
                    <h4 class="font-semibold text-md mb-2">Rincian Barang</h4>
                    <div class="border rounded-lg overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Nama Barang</th>
                                    <th class="px-4 py-2 text-center font-semibold text-gray-600">Jumlah</th>
                                    <template x-if="detail.status === 'Dikembalikan'">
                                        <th class="px-4 py-2 text-center font-semibold text-gray-600">Detail Units</th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="item in detail.detail_peminjaman" :key="item.id">
                                    <tr class="border-t">
                                        <td class="px-4 py-2" x-text="item.barang.nama_barang"></td>
                                        <td class="px-4 py-2 text-center" x-text="(item.peminjaman_units?.length ?? 0) + ' unit'"></td>
                                        <template x-if="detail.status === 'Dikembalikan'">
                                            <td class="px-4 py-2 text-center">
                                                <button @click="openUnitsModal(item)" class="px-3 py-1 text-xs font-medium text-white bg-purple-500 rounded hover:bg-purple-600">
                                                    View Units
                                                </button>
                                            </td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <template x-if="detail.history && detail.history.gambar_bukti">
                    <div>
                        <h4 class="font-semibold text-md mb-2">Bukti Pengembalian</h4>
                        <div class="border rounded-lg p-2">
                            <img :src="`/storage/${detail.history.gambar_bukti}`" alt="Bukti Pengembalian" class="w-full h-auto rounded-md">
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Modal Nested untuk Detail Units --}}
    <div x-show="isUnitsModalOpen" @keydown.escape.window="isUnitsModalOpen = false" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div @click="isUnitsModalOpen = false" x-show="isUnitsModalOpen" class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-60"></div>

            <div x-show="isUnitsModalOpen" class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl">
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
                            <div class="grid grid-cols-3 gap-2 text-sm">
                                <div>
                                    <p class="text-gray-600">Total Unit</p>
                                    <p class="text-lg font-bold" x-text="selectedItem?.peminjaman_units?.length ?? 0"></p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Dikembalikan Baik</p>
                                    <p class="text-lg font-bold text-green-600" x-text="getUnitsByStatus('dikembalikan')"></p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Rusak</p>
                                    <p class="text-lg font-bold text-red-600" x-text="getUnitsByStatus('rusak_ringan') + getUnitsByStatus('rusak_berat')"></p>
                                </div>
                            </div>
                        </div>

                <div class="border rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold">No</th>
                                <th class="px-4 py-2 text-left font-semibold">Kode Unit</th>
                                <th class="px-4 py-2 text-left font-semibold">Status</th>
                                <th class="px-4 py-2 text-left font-semibold">Keterangan</th>
                                <th class="px-4 py-2 text-center font-semibold">Foto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <template x-for="(unit, index) in selectedItem?.peminjaman_units" :key="unit.id">
                                <tr>
                                    <td class="px-4 py-3" x-text="index + 1"></td>
                                    <td class="px-4 py-3 font-mono text-xs" x-text="unit.barang_unit.unit_code"></td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded-full font-semibold"
                                              :class="{
                                                  'bg-green-100 text-green-700': unit.status_pengembalian === 'dikembalikan',
                                                  // both rusak_ringan and rusak_berat will match this
                                                  'bg-yellow-100 text-yellow-700': unit.status_pengembalian?.toLowerCase().includes('rusak'),
                                                  // removed 'hilang' branch because the status 'hilang' is no longer used
                                                  'bg-gray-100 text-gray-700': !unit.status_pengembalian
                                              }"
                                              x-text="unit.status_pengembalian.charAt(0).toUpperCase() + unit.status_pengembalian.slice(1)">
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs" x-text="unit.keterangan_kondisi || '-'"></td>
                                    <td class="px-4 py-3 text-center">
                                        <template x-if="unit.foto_kondisi">
                                            <button @click="showPhotoModal(unit.foto_kondisi)" class="text-blue-600 hover:text-blue-800 text-xs underline">
                                                Lihat Foto
                                            </button>
                                        </template>
                                        <template x-if="!unit.foto_kondisi">
                                            <span class="text-gray-400 text-xs">-</span>
                                        </template>
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

    {{-- Modal untuk Preview Foto --}}
    <div x-show="isPhotoModalOpen" @keydown.escape.window="isPhotoModalOpen = false" class="fixed inset-0 z-[70] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div @click="isPhotoModalOpen = false" x-show="isPhotoModalOpen" class="fixed inset-0 transition-opacity bg-black bg-opacity-75"></div>

            <div x-show="isPhotoModalOpen" class="inline-block max-w-4xl p-4 my-8 overflow-hidden transition-all transform bg-white rounded-lg shadow-xl">
                <div class="flex justify-end mb-2">
                    <button @click="isPhotoModalOpen = false" class="text-gray-600 hover:text-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <img :src="`/storage/${selectedPhoto}`" class="w-full h-auto rounded" alt="Foto Kondisi Unit">
            </div>
        </div>
    </div>
</div>

<script>
    function historyData(pengembalianSukses = false) {
        return {
            showModal: false,
            showSuccessModal: pengembalianSukses,
            loading: false,
            detail: null,
            isUnitsModalOpen: false,
            selectedItem: null,
            isPhotoModalOpen: false,
            selectedPhoto: null,

            fetchDetail(id) {
                this.showModal = true;
                this.loading = true;
                this.detail = null;

                // This creates the correct URL safely using the route name from web.php
                const url = `{{ route('user.history.show', ['id' => ':id']) }}`.replace(':id', id);

                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            // This will stop execution if the server response is 404, 500, etc.
                            throw new Error(`Network response was not ok. Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        this.detail = data; // 'detail' is now populated with data
                        this.loading = false;
                    })
                    .catch(error => {
                        this.loading = false;
                        console.error('Fetch Error:', error); // Shows the real error in the console
                        alert('Gagal memuat detail. Pastikan rute sudah benar dan tidak ada error server.');
                        this.showModal = false; // Close the modal on error
                    });
            },

            openUnitsModal(item) {
                this.selectedItem = item;
                this.isUnitsModalOpen = true;
            },

            getUnitsByStatus(status) {
                if (!this.selectedItem || !this.selectedItem.peminjaman_units) return 0;
                return this.selectedItem.peminjaman_units.filter(unit => unit.status_pengembalian === status).length;
            },

            showPhotoModal(photoPath) {
                this.selectedPhoto = photoPath;
                this.isPhotoModalOpen = true;
            }
        }
    }
</script>
@endsection
