<x-admin-layout>
        <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Konfirmasi Peminjaman dan Pengembalian') }}
        </h2>
    </x-slot>
    <div class="py-12 w-full">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="konfirmasiData()">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="space-y-8">
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Konfirmasi Peminjaman</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Nama Peminjam</th>
                                    <th class="px-6 py-3">NIM</th>
                                    <th class="px-6 py-3">Tanggal Checkout</th>
                                    <th class="px-6 py-3 text-center">Status</th>
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($peminjamanMenunggu as $peminjaman)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 align-middle">{{ $peminjaman->user->nama }}</td>
                                    <td class="px-6 py-4 align-middle">{{ $peminjaman->user->nim }}</td>
                                    <td class="px-6 py-4 align-middle">{{ $peminjaman->created_at->format('d F Y') }}</td>
                                    <td class="px-6 py-4 text-center align-middle">
                                        <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                            {{ $peminjaman->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center align-middle">
                                        <button @click="fetchDetail({{ $peminjaman->id }}, 'peminjaman')" class="text-blue-600 hover:text-blue-800">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-4">Tidak ada permintaan peminjaman baru.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Konfirmasi Pengembalian</h3>
                     <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Nama Peminjam</th>
                                    <th class="px-6 py-3">NIM</th>
                                    <th class="px-6 py-3">Tanggal Pengembalian</th>
                                    <th class="px-6 py-3 text-center">Status</th>
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pengembalianMenunggu as $peminjaman)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 align-middle">{{ $peminjaman->user->nama }}</td>
                                    <td class="px-6 py-4 align-middle">{{ $peminjaman->user->nim }}</td>
                                    <td class="px-6 py-4 align-middle">{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d F Y') }}</td>
                                    <td class="px-6 py-4 text-center align-middle">
                                        <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">
                                            {{ $peminjaman->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center align-middle">
                                        <button @click="fetchDetail({{ $peminjaman->id }}, 'pengembalian')" class="text-blue-600 hover:text-blue-800">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-4">Tidak ada permintaan pengembalian baru.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" style="display: none;">
                <div @click.outside="showModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-4 border-b pb-3">
                        <h3 class="text-xl font-bold text-gray-800" x-text="modalTitle"></h3>
                        <button @click="showModal = false" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
                    </div>
                    
                    <div x-show="loading" class="text-center py-10">Memuat data...</div>

                    <div x-show="!loading && detail" class="space-y-4">
                        {{-- Notifikasi Status Pengembalian dari History --}}
                        <template x-if="detail.history && modalType === 'pengembalian'">
                            <div class="p-4 rounded-lg" :class="{
                                'bg-green-100 border-l-4 border-green-500 text-green-700': detail.history.status_pengembalian == 'Aman',
                                'bg-red-100 border-l-4 border-red-500 text-red-700': detail.history.status_pengembalian.includes('Hilang'),
                                'bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700': detail.history.status_pengembalian.includes('Terlambat')
                            }">
                                <p class="font-bold">Status Pengembalian: <span x-text="detail.history.status_pengembalian"></span></p>
                            </div>
                        </template>

                        {{-- Tabel Rincian Barang Dipinjam --}}
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
                                        <template x-for="item in detail.detail_peminjamans" :key="item.id">
                                            <tr class="border-t">
                                                <td class="px-4 py-2 text-gray-800" x-text="item.barang.nama_barang"></td>
                                                <td class="px-4 py-2 text-center text-gray-600" x-text="(item.jumlah + item.jumlah_hilang) + ' pcs'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Menampilkan Barang yang Hilang (jika ada) --}}
                        <template x-if="modalType === 'pengembalian' && detail.detail_peminjamans.some(item => item.jumlah_hilang > 0)">
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
                                            <template x-for="item in detail.detail_peminjamans.filter(i => i.jumlah_hilang > 0)" :key="item.id">
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
                        
                        {{-- Deskripsi Kehilangan (jika ada) --}}
                        <template x-if="detail.history && detail.history.deskripsi_kehilangan">
                            <div>
                                <h4 class="font-semibold text-md mb-1">Alasan Kehilangan:</h4>
                                <p class="text-sm p-3 bg-gray-50 border rounded-md" x-text="detail.history.deskripsi_kehilangan"></p>
                            </div>
                        </template>

                        <div class="mt-6 flex justify-end gap-3">
                            <form :action="formActionTolak" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menolak permintaan ini?');">
                                @csrf
                                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">Tolak</button>
                            </form>
                            <form :action="formActionTerima" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui permintaan ini?');">
                                @csrf
                                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">Terima</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function konfirmasiData() {
            return {
                showModal: false,
                loading: false,
                detail: null,
                modalTitle: '',
                modalType: '', // 'peminjaman' atau 'pengembalian'
                formActionTerima: '',
                formActionTolak: '',
                fetchDetail(id, type) {
                    this.showModal = true;
                    this.loading = true;
                    this.detail = null;
                    this.modalType = type;
                    this.modalTitle = type === 'peminjaman' ? 'Detail Permintaan Peminjaman' : 'Detail Permintaan Pengembalian';
                    
                    // Menggunakan nama rute yang sudah ada
                    const terimaRoute = type === 'peminjaman' ? '{{ route("admin.konfirmasi.peminjaman.terima", ["id" => ":id"]) }}' : '{{ route("admin.konfirmasi.pengembalian.terima", ["id" => ":id"]) }}';
                    const tolakRoute = type === 'peminjaman' ? '{{ route("admin.konfirmasi.peminjaman.tolak", ["id" => ":id"]) }}' : '{{ route("admin.konfirmasi.pengembalian.tolak", ["id" => ":id"]) }}';

                    this.formActionTerima = terimaRoute.replace(':id', id);
                    this.formActionTolak = tolakRoute.replace(':id', id);

                    fetch(`/admin/konfirmasi/${id}`)
                        .then(response => {
                            if (!response.ok) throw new Error('Gagal mengambil data');
                            return response.json();
                        })
                        .then(data => {
                            this.detail = data;
                            this.loading = false;
                        })
                        .catch(() => {
                            this.loading = false;
                            alert('Gagal memuat detail.');
                            this.showModal = false;
                        });
                }
            }
        }
    </script>
    @endpush
</x-admin-layout>