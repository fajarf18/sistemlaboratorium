<x-admin-layout>
    {{-- 
        Bungkus utama Alpine.js. 
        x-data="konfirmasiData()" sekarang mencakup semua elemen di dalamnya,
        termasuk tombol, notifikasi, dan modal.
    --}}
    <div x-data="konfirmasiData()">
        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-100 border border-green-300" role="alert">
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 inline w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span class="font-medium">Sukses!</span>
                        <span class="ml-1">{{ session('success') }}</span>
                        <button type="button" @click="show = false" class="ml-auto -mx-1.5 -my-1.5 bg-green-100 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex h-8 w-8">
                            <span class="sr-only">Dismiss</span>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        </button>
                    </div>
                </div>
            @endif

            @if (session('danger'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-100 border border-red-300" role="alert">
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 inline w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <span class="font-medium">Info:</span>
                        <span class="ml-1">{{ session('danger') }}</span>
                        <button type="button" @click="show = false" class="ml-auto -mx-1.5 -my-1.5 bg-red-100 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex h-8 w-8">
                            <span class="sr-only">Dismiss</span>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        </button>
                    </div>
                </div>
            @endif
            {{-- Menunggu Konfirmasi Peminjaman --}}
            <div class="bg-white shadow-lg rounded-sm border border-gray-200 mb-8" x-data="{ open: true }">
                <header class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="font-semibold text-gray-800">Menunggu Konfirmasi Peminjaman</h2>
                    <button @click="open = !open">
                        <svg class="w-6 h-6" :class="{ 'transform rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                </header>
                <div class="p-3" x-show="open" x-transition>
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full">
                            <thead class="text-xs font-semibold uppercase text-gray-400 bg-gray-50">
                                <tr>
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Nama Peminjam</div></th>
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Tanggal Pinjam</div></th>
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Tanggal Wajib Kembali</div></th>
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-center">Aksi</div></th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                @forelse ($peminjamanMenunggu as $item)
                                    <tr>
                                        <td class="p-2 whitespace-nowrap">{{ $item->user->nama }}</td>
                                        <td class="p-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->isoFormat('D MMMM YYYY') }}</td>
                                        <td class="p-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_wajib_kembali)->isoFormat('D MMMM YYYY') }}</td>
                                        <td class="p-2 whitespace-nowrap text-center">
                                            <button @click="openModal('peminjaman', {{ $item->id }})" class="px-3 py-1 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">
                                                Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="p-4 text-center text-gray-500">Tidak ada permintaan peminjaman.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Menunggu Konfirmasi Pengembalian --}}
            <div class="bg-white shadow-lg rounded-sm border border-gray-200" x-data="{ open: true }">
                <header class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="font-semibold text-gray-800">Menunggu Konfirmasi Pengembalian</h2>
                    <button @click="open = !open">
                        <svg class="w-6 h-6" :class="{ 'transform rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                </header>
                <div class="p-3" x-show="open" x-transition>
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full">
                             <thead class="text-xs font-semibold uppercase text-gray-400 bg-gray-50">
                                <tr>
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Nama Peminjam</div></th>
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Tanggal Pinjam</div></th>
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Tanggal Wajib Kembali</div></th>
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-center">Aksi</div></th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                @forelse ($pengembalianMenunggu as $item)
                                    <tr>
                                        <td class="p-2 whitespace-nowrap">{{ $item->user->nama }}</td>
                                        <td class="p-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->isoFormat('D MMMM YYYY') }}</td>
                                        <td class="p-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_wajib_kembali)->isoFormat('D MMMM YYYY') }}</td>
                                        <td class="p-2 whitespace-nowrap text-center">
                                            <button @click="openModal('pengembalian', {{ $item->id }})" class="px-3 py-1 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">
                                                Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                     <tr><td colspan="4" class="p-4 text-center text-gray-500">Tidak ada permintaan pengembalian.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Detail --}}
        {{-- Modal Detail --}}
<div x-show="isModalOpen" @keydown.escape.window="isModalOpen = false" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
    <div class="flex items-end justify-center min-h-screen px-4 text-center md:items-center sm:block sm:p-0">
        <div x-cloak @click="isModalOpen = false" x-show="isModalOpen" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-40" aria-hidden="true"></div>

        <div x-cloak x-show="isModalOpen" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-xl p-8 my-20 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl 2xl:max-w-2xl">
            <div class="flex items-center justify-between space-x-4">
                <h1 class="text-xl font-medium text-gray-800" id="modal-title" x-text="'Detail ' + (modalType.charAt(0).toUpperCase() + modalType.slice(1))"></h1>
                <button @click="isModalOpen = false" class="text-gray-600 focus:outline-none hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </button>
            </div>

            <div class="mt-6">
                <div x-show="loading" class="flex justify-center items-center py-10">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                </div>

                <div x-show="!loading && detail" class="space-y-4">
                    <template x-if="detail">
                        <div>
                            <template x-if="modalType === 'pengembalian' && detail.status_pengembalian">
                                <div class="p-4 mb-4 text-sm rounded-lg border-l-4"
                                     :class="{
                                        'bg-red-100 border-red-500 text-red-800': detail.status_pengembalian.includes('Hilang'),
                                        'bg-blue-100 border-blue-500 text-blue-800': !detail.status_pengembalian.includes('Hilang') && detail.status_pengembalian.includes('Habis'),
                                        'bg-yellow-100 border-yellow-500 text-yellow-800': !detail.status_pengembalian.includes('Hilang') && !detail.status_pengembalian.includes('Habis') && detail.status_pengembalian.includes('Terlambat'),
                                        'bg-green-100 border-green-500 text-green-800': detail.status_pengembalian === 'Aman'
                                     }">
                                    <p class="font-bold text-lg" x-text="detail.status_pengembalian"></p>
                                    
                                    <ul class="mt-2 list-disc list-inside">
                                        <template x-if="detail.hari_terlambat && detail.hari_terlambat > 0">
                                            <li x-text="`Terlambat ${detail.hari_terlambat} hari.`"></li>
                                        </template>
                                        <template x-if="detail.total_hilang && detail.total_hilang > 0">
                                            <li x-text="`Kehilangan ${detail.total_hilang} unit barang.`"></li>
                                        </template>
                                        <template x-if="detail.total_habis && detail.total_habis > 0">
                                            <li x-text="`Barang habis pakai digunakan: ${detail.total_habis} unit.`"></li>
                                        </template>
                                    </ul>
                                    </div>
                            </template>
                            <div>
                                <h3 class="font-semibold text-lg text-gray-800 mb-2">Informasi Peminjam</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                    <div>
                                        <p class="font-semibold text-gray-500">Nama</p>
                                        <p class="text-gray-900" x-text="detail.user.nama"></p>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-500">NIM/NIP</p>
                                        <p class="text-gray-900" x-text="detail.user.nim"></p>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-500">Email</p>
                                        <p class="text-gray-900" x-text="detail.user.email"></p>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-500">Prodi</p>
                                        <p class="text-gray-900" x-text="detail.user.prodi"></p>
                                    </div>
                                </div>
                                <hr class="my-4">
                            </div>
                            <h4 class="font-semibold text-md mb-2">Rincian Barang</h4>
                            <div class="border rounded-lg overflow-hidden mb-4">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Nama Barang</th>
                                            <th class="px-4 py-2 text-center font-semibold text-gray-600">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="item in detail.detail_peminjaman" :key="item.id">
                                            <tr class="border-t">
                                                <td class="px-4 py-2" x-text="item.barang.nama_barang"></td>
                                                <td class="px-4 py-2 text-center" x-text="item.jumlah + ' pcs'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            
                            <template x-if="modalType === 'pengembalian' && detail.history && detail.history.gambar_bukti">
                                <div>
                                    <h4 class="font-semibold text-md mb-2">Bukti Pengembalian</h4>
                                    <div class="border rounded-lg p-2">
                                        <img :src="`{{ asset('storage') }}/${detail.history.gambar_bukti}`" alt="Bukti Pengembalian" class="w-full h-auto rounded-md">
                                    </div>
                                </div>
                            </template>

                            <div class="flex justify-end mt-6 space-x-2">
                                <button @click="isModalOpen = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Tutup</button>
                                <form :action="modalType === 'peminjaman' ? `{{ url('admin/konfirmasi/tolak-peminjaman') }}/${detail.id}` : `{{ url('admin/konfirmasi/tolak-pengembalian') }}/${detail.id}`" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Tolak</button>
                                </form>
                                <form :action="modalType === 'peminjaman' ? `{{ url('admin/konfirmasi/terima-peminjaman') }}/${detail.id}` : `{{ url('admin/konfirmasi/terima-pengembalian') }}/${detail.id}`" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Terima</button>
                                </form>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>  

    @push('scripts')
    <script>
        function konfirmasiData() {
            return {
                isModalOpen: false,
                modalType: '',
                detail: null,
                loading: false,
                openModal(type, id) {
                    this.isModalOpen = true;
                    this.modalType = type;
                    this.fetchDetail(id);
                },
                fetchDetail(id) {
                    this.loading = true;
                    this.detail = null;
                    fetch(`/admin/konfirmasi/${id}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            this.detail = data;
                            this.loading = false;
                        })
                        .catch(error => {
                            console.error('Error fetching detail:', error);
                            this.loading = false;
                        });
                }
            };
        }
    </script>
    @endpush
</x-admin-layout>