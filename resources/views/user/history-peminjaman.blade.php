@extends('layouts.app')

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
                    <th scope="col" class="px-6 py-3">Tanggal Kembali</th>
                    <th scope="col" class="px-6 py-3 text-center">Status</th>
                    <th scope="col" class="px-6 py-3 text-center">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($historyPeminjamans as $history)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 align-middle">{{ \Carbon\Carbon::parse($history->tanggal_pinjam)->format('d F Y') }}</td>
                    <td class="px-6 py-4 align-middle">{{ $history->tanggal_kembali ? \Carbon\Carbon::parse($history->tanggal_kembali)->format('d F Y') : '-' }}</td>
                    <td class="px-6 py-4 text-center align-middle">
                        <span class="inline-block w-40 text-center px-3 py-1 text-xs font-medium rounded-full 
                            @if($history->status == 'Tunggu Konfirmasi Admin') bg-yellow-100 text-yellow-800
                            @elseif($history->status == 'Dipinjam') bg-green-100 text-green-800
                            @elseif($history->status == 'Dikembalikan') bg-blue-100 text-blue-800
                            @elseif($history->status == 'Ditolak') bg-red-100 text-red-800
                            @endif">
                            {{ $history->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center align-middle">
                        <button @click="fetchDetail({{ $history->id }})" class="text-blue-600 hover:underline">
                            Detail
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-10 text-gray-500">Belum ada history peminjaman.</td>
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
        <div @click.outside="showModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-xl font-bold text-gray-800">Detail Peminjaman</h3>
                <button @click="showModal = false" class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
            </div>
            
            <div x-show="loading" class="text-center py-10">Memuat data...</div>

            <div x-show="!loading && detail" class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500">Tanggal Pinjam</p>
                    <p class="font-semibold" x-text="new Date(detail.tanggal_pinjam).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })"></p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Barang yang Dipinjam:</p>
                    <ul class="list-disc list-inside mt-1 space-y-1">
                        <template x-for="item in detail.detail_peminjamans" :key="item.id">
                            <li x-text="`${item.barang.nama_barang} (${item.jumlah + item.jumlah_hilang} pcs)`"></li>
                        </template>
                    </ul>
                </div>

                <div x-show="detail.detail_peminjamans.some(item => item.jumlah_hilang > 0)">
                    <p class="text-sm text-gray-500">Barang yang Hilang:</p>
                    <ul class="list-disc list-inside mt-1 space-y-1 text-red-600">
                        <template x-for="item in detail.detail_peminjamans.filter(i => i.jumlah_hilang > 0)" :key="item.id">
                            <li x-text="`${item.barang.nama_barang} (${item.jumlah_hilang} pcs)`"></li>
                        </template>
                    </ul>
                </div>

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
            }
        }
    }
</script>
@endsection