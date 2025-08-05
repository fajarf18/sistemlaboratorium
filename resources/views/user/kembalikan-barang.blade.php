@extends('layouts.app')

@section('header', 'Kembalikan Barang')

@section('content')
{{-- Kirim status error ke Alpine.js untuk membuka kembali modal jika validasi gagal --}}
<div class="bg-white p-4 sm:p-6 rounded-xl shadow-lg" x-data="kembalikanBarangData({{ $detailPeminjamans }}, {{ $errors->any() ? 'true' : 'false' }})">
    
    {{-- Notifikasi Global (jika ada error yang tidak terkait validasi form) --}}
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div class="relative w-full md:w-auto md:flex-1">
            <input type="text" placeholder="Search Item..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button @click="cekBarang" :disabled="items.length === 0" class="w-full md:w-auto px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition disabled:bg-gray-400">
            Cek
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Item Name</th>
                    <th scope="col" class="px-6 py-3">Tipe</th>
                    <th scope="col" class="px-6 py-3">Jumlah Dipinjam</th>
                    <th scope="col" class="px-6 py-3 text-center">Jumlah Dikembalikan</th>
                    <th scope="col" class="px-6 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(detail, index) in items" :key="detail.id">
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap align-middle" x-text="detail.barang.nama_barang"></th>
                        <td class="px-6 py-4 align-middle" x-text="detail.barang.tipe"></td>
                        <td class="px-6 py-4 align-middle" x-text="detail.jumlah + ' pcs'"></td>
                        <td class="px-6 py-4 align-middle whitespace-nowrap">
                            <div class="inline-flex items-center justify-center space-x-2">
                                <button @click="if (detail.jumlahDikembalikan > 0) detail.jumlahDikembalikan--" class="px-2 py-1 border rounded-md">-</button>
                                <input type="number" x-model.number="detail.jumlahDikembalikan" min="0" :max="detail.jumlah" class="w-16 text-center border-gray-300 rounded-md">
                                <button @click="if (detail.jumlahDikembalikan < detail.jumlah) detail.jumlahDikembalikan++" class="px-2 py-1 border rounded-md">+</button>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center align-middle">
                            <span x-show="detail.jumlahDikembalikan == detail.jumlah" class="inline-block w-24 text-center px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Aman</span>
                            <span x-show="detail.jumlahDikembalikan < detail.jumlah" class="inline-block w-24 text-center px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800" style="display: none;">Hilang</span>
                        </td>
                    </tr>
                </template>
                @if($detailPeminjamans->isEmpty())
                <tr>
                    <td colspan="5" class="text-center py-10 text-gray-500">Tidak ada barang yang perlu dikembalikan saat ini.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Modal Konfirmasi -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" style="display: none;">
        <div @click.outside="showModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
            <form action="{{ route('user.kembalikan.konfirmasi') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="items" :value="JSON.stringify(items)">

                {{-- Menampilkan Error Validasi di Dalam Modal --}}
                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-r-lg" role="alert">
                        <p class="font-bold">Terjadi Kesalahan</p>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <template x-if="barangHilang.length > 0">
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-r-lg" role="alert">
                        <p class="font-bold">Terdeteksi <span x-text="barangHilang.length"></span> Jenis Barang Hilang!</p>
                    </div>
                </template>
                
                <template x-if="hariTerlambat > 0">
                    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded-r-lg" role="alert">
                        <p class="font-bold">Pengembalian Terlambat <span x-text="hariTerlambat"></span> Hari!</p>
                    </div>
                </template>
                
                <h3 class="text-xl font-bold text-gray-800 mb-4">Cek Barang</h3>
                
                <div class="space-y-4">
                    <template x-if="barangHilang.length > 0">
                        <div class="border rounded-lg p-3">
                            <p class="font-semibold mb-2">Rincian Barang Hilang:</p>
                            <ul>
                                <template x-for="item in barangHilang" :key="item.id">
                                    <li class="text-sm text-gray-700">- <span x-text="item.barang.nama_barang"></span> (<span x-text="item.jumlah - item.jumlahDikembalikan"></span> pcs hilang)</li>
                                </template>
                            </ul>
                        </div>
                    </template>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Pinjam</label>
                            <input type="text" :value="tanggalPinjamFormatted" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                        </div>
                        <div>
                            <label for="tanggal_kembali" class="block text-sm font-medium text-gray-700">Tanggal Dikembalikan</label>
                            <input type="date" id="tanggal_kembali" name="tanggal_kembali" required :min="tanggalPinjam" x-model="tanggalKembali" @change="cekKeterlambatan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label for="gambar_bukti" class="block text-sm font-medium text-gray-700">Image (Bukti Pengembalian)</label>
                        <input type="file" id="gambar_bukti" name="gambar_bukti" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <template x-if="barangHilang.length > 0">
                        <div>
                            <label for="deskripsi_kehilangan" class="block text-sm font-medium text-gray-700">Description (Alasan Kehilangan)</label>
                            <textarea id="deskripsi_kehilangan" name="deskripsi_kehilangan" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                        </div>
                    </template>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Kembali</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function kembalikanBarangData(items, hasErrors = false) {
        return {
            items: items.map(item => ({...item, jumlahDikembalikan: item.jumlah })),
            showModal: hasErrors,
            barangHilang: [],
            tanggalPinjam: items.length > 0 ? items[0].peminjaman.tanggal_pinjam : '',
            tanggalWajibKembali: items.length > 0 ? items[0].peminjaman.tanggal_wajib_kembali : '',
            tanggalKembali: new Date().toLocaleDateString('en-CA'),
            hariTerlambat: 0,
            
            get tanggalPinjamFormatted() {
                return this.tanggalPinjam ? new Date(this.tanggalPinjam).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '';
            },

            cekKeterlambatan() {
                if (!this.tanggalKembali || !this.tanggalWajibKembali) return;
                const tglKembali = new Date(this.tanggalKembali);
                const tglWajib = new Date(this.tanggalWajibKembali);
                if (tglKembali > tglWajib) {
                    const diffTime = tglKembali - tglWajib;
                    this.hariTerlambat = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                } else {
                    this.hariTerlambat = 0;
                }
            },
            
            cekBarang() {
                this.barangHilang = this.items.filter(item => item.jumlahDikembalikan < item.jumlah);
                this.cekKeterlambatan();
                this.showModal = true;
            }
        }
    }
</script>
@endsection