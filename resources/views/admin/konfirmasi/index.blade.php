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
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Dosen Pengampu</div></th>
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Tanggal Pinjam</div></th>
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Tanggal Wajib Kembali</div></th>
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Konfirmasi Laboran</div></th>
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-center">Aksi</div></th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                @forelse ($peminjamanMenunggu as $item)
                                    <tr class="{{ $item->dosen_konfirmasi_at ? 'bg-emerald-50' : '' }}">
                                        <td class="p-2 whitespace-nowrap">{{ $item->user->nama }}</td>
                                        <td class="p-2 whitespace-nowrap">
                                            @if($item->dosen)
                                                <div class="font-medium text-gray-900">{{ $item->dosen->nama }}</div>
                                            @elseif($item->kelasPraktikum && $item->kelasPraktikum->modul)
                                                <div class="font-medium text-gray-900">{{ $item->kelasPraktikum->modul->user->nama ?? '-' }}</div>
                                                <div class="text-xs text-blue-500">Kelas: {{ $item->kelasPraktikum->nama_kelas }}</div>
                                            @else
                                                <span class="text-gray-400 italic">-</span>
                                            @endif
                                        </td>
                                        <td class="p-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->isoFormat('D MMMM YYYY') }}</td>
                                        <td class="p-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_wajib_kembali)->isoFormat('D MMMM YYYY') }}</td>
                                        <td class="p-2 whitespace-nowrap">
                                            @if($item->dosen_konfirmasi_at)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800" title="Dikonfirmasi pada {{ \Carbon\Carbon::parse($item->dosen_konfirmasi_at)->isoFormat('D MMMM YYYY HH:mm') }}">
                                                    ✓ Dikonfirmasi Dosen
                                                </span>
                                            @elseif($item->dosen && $item->dosen->id)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Menunggu Konfirmasi Laboran
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Tidak Perlu Konfirmasi Dosen
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-2 whitespace-nowrap text-center">
                                            <button @click="openModal('peminjaman', {{ $item->id }})" class="px-3 py-1 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">
                                                Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="p-4 text-center text-gray-500">Tidak ada permintaan peminjaman.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Status Pengguna (Peminjaman Aktif) --}}
            <div class="bg-white shadow-lg rounded-sm border border-gray-200 mb-8" x-data="{ open: true }">
                <header class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="font-semibold text-gray-800">Status Pengguna</h2>
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
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Dosen Pengampu</div></th>
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Tanggal Pinjam</div></th>
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Tanggal Wajib Kembali</div></th>
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Status</div></th>
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-center">Aksi</div></th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                @forelse ($statusPeminjam as $item)
                                    <tr>
                                        <td class="p-2 whitespace-nowrap">{{ $item->user->nama }}</td>
                                        <td class="p-2 whitespace-nowrap">
                                            @if($item->dosen)
                                                <div class="font-medium text-gray-900">{{ $item->dosen->nama }}</div>
                                            @elseif($item->kelasPraktikum && $item->kelasPraktikum->modul)
                                                <div class="font-medium text-gray-900">{{ $item->kelasPraktikum->modul->user->nama ?? '-' }}</div>
                                                <div class="text-xs text-blue-500">Kelas: {{ $item->kelasPraktikum->nama_kelas }}</div>
                                            @else
                                                <span class="text-gray-400 italic">-</span>
                                            @endif
                                        </td>
                                        <td class="p-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->isoFormat('D MMMM YYYY') }}</td>
                                        <td class="p-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_wajib_kembali)->isoFormat('D MMMM YYYY') }}</td>
                                        <td class="p-2 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                @if($item->status == 'Dipinjam') bg-green-100 text-green-800
                                                @elseif($item->status == 'Menunggu Konfirmasi') bg-yellow-100 text-yellow-800
                                                @elseif($item->status == 'Tunggu Konfirmasi Admin') bg-orange-100 text-orange-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td class="p-2 whitespace-nowrap text-center">
                                            <button @click="openModal('status', {{ $item->id }})" class="px-3 py-1 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">
                                                Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="p-4 text-center text-gray-500">Tidak ada status pengguna aktif.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <hr class="my-8 border-gray-300">

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
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Dosen Pengampu</div></th>
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Tanggal Pinjam</div></th>
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Tanggal Wajib Kembali</div></th>
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Tanggal Kembali</div></th>
                                    <th class="p-2 whitespace-nowrap"><div class="font-semibold text-center">Aksi</div></th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                @forelse ($pengembalianMenunggu as $item)
                                    <tr>
                                        <td class="p-2 whitespace-nowrap">{{ $item->user->nama }}</td>
                                        <td class="p-2 whitespace-nowrap">
                                            @if($item->dosen)
                                                <div class="font-medium text-gray-900">{{ $item->dosen->nama }}</div>
                                            @elseif($item->kelasPraktikum && $item->kelasPraktikum->modul)
                                                <div class="font-medium text-gray-900">{{ $item->kelasPraktikum->modul->user->nama ?? '-' }}</div>
                                                <div class="text-xs text-blue-500">Kelas: {{ $item->kelasPraktikum->nama_kelas }}</div>
                                            @else
                                                <span class="text-gray-400 italic">-</span>
                                            @endif
                                        </td>
                                        <td class="p-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->isoFormat('D MMMM YYYY') }}</td>
                                        <td class="p-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_wajib_kembali)->isoFormat('D MMMM YYYY') }}</td>
                                        <td class="p-2 whitespace-nowrap">
                                            {{ $item->tanggal_kembali ? \Carbon\Carbon::parse($item->tanggal_kembali)->isoFormat('D MMMM YYYY') : '-' }}
                                        </td>
                                        <td class="p-2 whitespace-nowrap text-center">
                                            <button @click="openModal('pengembalian', {{ $item->id }})" class="px-3 py-1 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">
                                                Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                     <tr><td colspan="5" class="p-4 text-center text-gray-500">Tidak ada permintaan pengembalian.</td></tr>
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
                                                     // use yellow for any 'rusak' return statuses
                                                     'bg-yellow-100 border-yellow-500 text-yellow-800': detail.status_pengembalian.toLowerCase().includes('rusak'),
                                                     'bg-blue-100 border-blue-500 text-blue-800': !detail.status_pengembalian.toLowerCase().includes('rusak') && detail.status_pengembalian.toLowerCase().includes('habis'),
                                                     'bg-yellow-100 border-yellow-500 text-yellow-800': !detail.status_pengembalian.toLowerCase().includes('rusak') && !detail.status_pengembalian.toLowerCase().includes('habis') && detail.status_pengembalian.toLowerCase().includes('terlambat'),
                                                     'bg-green-100 border-green-500 text-green-800': detail.status_pengembalian === 'Aman'
                                                 }">
                                    <p class="font-bold text-lg" x-text="detail.status_pengembalian"></p>
                                    
                                    <ul class="mt-2 list-disc list-inside">
                                        <template x-if="detail.hari_terlambat && detail.hari_terlambat > 0">
                                            <li x-text="`Terlambat ${detail.hari_terlambat} hari.`"></li>
                                        </template>
                                        <template x-if="detail.total_hilang && detail.total_hilang > 0">
                                            <li x-text="`Kerusakan ${detail.total_hilang} unit barang.`"></li>
                                        </template>
                                        <template x-if="detail.total_habis && detail.total_habis > 0">
                                            <li x-text="`Barang habis pakai digunakan: ${detail.total_habis} unit.`"></li>
                                        </template>
                                    </ul>
                                    </div>
                            </template>
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                                <div>
                                    <p class="text-gray-600">Tanggal Pinjam</p>
                                    <p class="font-semibold text-gray-900" x-text="detail.tanggal_pinjam ? new Date(detail.tanggal_pinjam).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'"></p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Tanggal Wajib Kembali</p>
                                    <p class="font-semibold text-gray-900" x-text="detail.tanggal_wajib_kembali ? new Date(detail.tanggal_wajib_kembali).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'"></p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Tanggal Dikembalikan</p>
                                    <p class="font-semibold text-gray-900" x-text="detail.tanggal_kembali ? new Date(detail.tanggal_kembali).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'"></p>
                                </div>
                            </div>
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
                                    <!-- Added Dosen Info -->
                                    <div class="col-span-1 sm:col-span-2 mt-2 pt-2 border-t border-gray-100">
                                        <p class="font-semibold text-gray-500">Dosen Pengampu</p>
                                        <template x-if="detail.dosen">
                                            <div>
                                                <p class="text-gray-900 font-medium" x-text="detail.dosen.nama"></p>
                                            </div>
                                        </template>
                                        <template x-if="!detail.dosen && detail.kelas_praktikum && detail.kelas_praktikum.modul">
                                            <div>
                                                <p class="text-gray-900 font-medium" x-text="detail.kelas_praktikum.modul.user?.nama || '-'"></p>
                                                <p class="text-xs text-blue-500" x-text="`Kelas: ${detail.kelas_praktikum.nama_kelas}`"></p>
                                            </div>
                                        </template>
                                        <template x-if="!detail.dosen && (!detail.kelas_praktikum || !detail.kelas_praktikum.modul)">
                                            <p class="text-gray-400 italic">Tidak ada dosen pengampu (Peminjaman Mandiri)</p>
                                        </template>
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
                                            <th class="px-4 py-2 text-center font-semibold text-gray-600">Detail Units</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="item in detail.detail_peminjaman" :key="item.id">
                                            <tr class="border-t">
                                                <td class="px-4 py-2">
                                                    <div class="font-semibold text-gray-800" x-text="item.barang.nama_barang"></div>
                                                    <div class="text-xs text-gray-500" x-text="item.barang.tipe"></div>
                                                    
                                                    {{-- Display Damaged Units directly here --}}
                                                    <template x-if="item.peminjaman_units && item.peminjaman_units.some(u => (u.status_pengembalian && u.status_pengembalian.includes('rusak')))">
                                                        <div class="mt-2 p-2 bg-red-50 rounded border border-red-100">
                                                            <p class="text-xs font-bold text-red-600 mb-1">Unit Bermasalah:</p>
                                                            <ul class="list-disc list-inside text-xs text-red-600 space-y-0.5">
                                                                <template x-for="unit in item.peminjaman_units.filter(u => (u.status_pengembalian && u.status_pengembalian.includes('rusak')))">
                                                                     <li>
                                                                        <span x-text="unit.barang_unit.unit_code" class="font-mono font-semibold"></span>
                                                                        <span x-text="` - ${unit.status_pengembalian.replace('_', ' ')}`" class="italic"></span>
                                                                     </li>
                                                                </template>
                                                            </ul>
                                                        </div>
                                                    </template>
                                                </td>
                                                <td class="px-4 py-2 text-center">
                                                    <span x-text="item.barang.tipe.toLowerCase() === 'habis pakai' ? `${item.jumlah} unit` : `${item.peminjaman_units.length} unit`"></span>
                                                </td>
                                                <td class="px-4 py-2 text-center">
                                                    <button @click="openUnitsModal(item)" class="px-3 py-1 text-xs font-medium text-white bg-purple-500 rounded hover:bg-purple-600">
                                                        View Units
                                                    </button>
                                                </td>
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
                                
                                {{-- Actions for Peminjaman --}}
                                <template x-if="modalType === 'peminjaman'">
                                    <div class="flex space-x-2">
                                        <form :action="`{{ url('admin/konfirmasi/tolak-peminjaman') }}/${detail.id}`" method="POST">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Tolak</button>
                                        </form>
                                        <form :action="`{{ url('admin/konfirmasi/terima-peminjaman') }}/${detail.id}`" method="POST">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Terima</button>
                                        </form>
                                    </div>
                                </template>

                                {{-- Actions for Pengembalian --}}
                                <template x-if="modalType === 'pengembalian'">
                                    <div class="flex space-x-2">
                                        <form :action="`{{ url('admin/konfirmasi/tolak-pengembalian') }}/${detail.id}`" method="POST">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Tolak</button>
                                        </form>
                                        <form :action="`{{ url('admin/konfirmasi/terima-pengembalian') }}/${detail.id}`" method="POST">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Terima</button>
                                        </form>
                                    </div>
                                </template>

                                {{-- Actions for Status --}}
                                <template x-if="modalType === 'status'">
                                    <div class="flex space-x-2">
                                        <template x-if="detail.status === 'Dipinjam'">
                                            <form :action="`{{ url('admin/status/selesaikan') }}/${detail.id}`" method="POST">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menyelesaikan peminjaman ini? Barang akan dikembalikan ke stok.')" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">Selesaikan (Kembali)</button>
                                            </form>
                                        </template>
                                        <template x-if="detail.status === 'Menunggu Konfirmasi'">
                                             <form :action="`{{ url('admin/status/batalkan') }}/${detail.id}`" method="POST">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin membatalkan peminjaman ini?')" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Batalkan</button>
                                            </form>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
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

            {{-- Ringkasan untuk Pengembalian --}}
            <template x-if="modalType === 'pengembalian'">
                <div class="mb-4 p-3 bg-gray-50 rounded">
                    <template x-if="selectedItem?.barang?.tipe?.toLowerCase() === 'habis pakai'">
                        <div class="grid grid-cols-3 gap-2 text-sm">
                            <div>
                                <p class="text-gray-600">Total Dipinjam</p>
                                <p class="text-lg font-bold" x-text="selectedItem?.jumlah ?? 0"></p>
                            </div>
                            <div>
                                <p class="text-gray-600">Dikembalikan</p>
                                <p class="text-lg font-bold text-green-600" x-text="(selectedItem?.jumlah ?? 0) - (selectedItem?.jumlah_rusak ?? 0)"></p>
                            </div>
                            <div>
                                <p class="text-gray-600">Terpakai</p>
                                <p class="text-lg font-bold text-red-600" x-text="selectedItem?.jumlah_rusak ?? 0"></p>
                            </div>
                        </div>
                    </template>
                    <template x-if="selectedItem?.barang?.tipe?.toLowerCase() !== 'habis pakai'">
                        <div class="grid grid-cols-3 gap-2 text-sm">
                            <div>
                                <p class="text-gray-600">Total Unit</p>
                                <p class="text-lg font-bold" x-text="selectedItem?.jumlah"></p>
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
                    </template>
                </div>
            </template>

            {{-- Ringkasan untuk Peminjaman --}}
            <template x-if="modalType === 'peminjaman'">
                <div class="mb-4 p-3 bg-blue-50 rounded border-l-4 border-blue-500">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-700">Unit yang akan dipinjam:</p>
                            <p class="text-lg font-bold text-blue-700" x-text="selectedItem?.jumlah + ' unit'"></p>
                        </div>
                    </div>
                </div>
            </template>

            <template x-if="selectedItem?.barang?.tipe?.toLowerCase() === 'habis pakai'">
                <div class="mt-4 p-4 border rounded-lg bg-blue-50">
                    <p class="text-sm text-gray-700">Barang habis pakai tidak memiliki detail unit. Ringkasan:</p>
                    <ul class="mt-2 text-sm text-gray-800 space-y-1">
                        <li><strong>Dipinjam:</strong> <span x-text="selectedItem?.jumlah ?? 0"></span> unit</li>
                        <li><strong>Dikembalikan:</strong> <span x-text="(selectedItem?.jumlah ?? 0) - (selectedItem?.jumlah_rusak ?? 0)"></span> unit</li>
                        <li><strong>Terpakai:</strong> <span x-text="selectedItem?.jumlah_rusak ?? 0"></span> unit</li>
                    </ul>
                </div>
            </template>
            <template x-if="selectedItem?.barang?.tipe?.toLowerCase() !== 'habis pakai'">
                <div class="border rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold">No</th>
                                <th class="px-4 py-2 text-left font-semibold">Kode Unit</th>
                                <th class="px-4 py-2 text-left font-semibold">Status Unit</th>
                                <template x-if="modalType === 'pengembalian'">
                                    <th class="px-4 py-2 text-left font-semibold">Status Pengembalian</th>
                                </template>
                                <template x-if="modalType === 'pengembalian'">
                                    <th class="px-4 py-2 text-left font-semibold">Keterangan</th>
                                </template>
                                <template x-if="modalType === 'pengembalian'">
                                    <th class="px-4 py-2 text-center font-semibold">Foto</th>
                                </template>
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
                                                  'bg-green-100 text-green-700': unit.barang_unit.status === 'baik',
                                                  'bg-yellow-100 text-yellow-700': unit.barang_unit.status?.toLowerCase().includes('rusak'),
                                                  'bg-blue-100 text-blue-700': unit.barang_unit.status === 'dipinjam'
                                              }"
                                              x-text="unit.barang_unit.status?.replaceAll('_',' ')">
                                        </span>
                                    </td>
                                    <template x-if="modalType === 'pengembalian'">
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full font-semibold"
                                                  :class="{
                                                      'bg-green-100 text-green-700': unit.status_pengembalian === 'dikembalikan',
                                                      'bg-yellow-100 text-yellow-700': unit.status_pengembalian?.toLowerCase()?.includes('rusak'),
                                                      'bg-gray-100 text-gray-700': unit.status_pengembalian === 'belum'
                                                  }"
                                                  x-text="unit.status_pengembalian?.replaceAll('_',' ')?.charAt(0).toUpperCase() + unit.status_pengembalian?.replaceAll('_',' ')?.slice(1)">
                                            </span>
                                        </td>
                                    </template>
                                    <template x-if="modalType === 'pengembalian'">
                                        <td class="px-4 py-3 text-xs" x-text="unit.keterangan_kondisi || '-'"></td>
                                    </template>
                                    <template x-if="modalType === 'pengembalian'">
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
                                    </template>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </template>

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
            <img :src="`{{ asset('storage') }}/${selectedPhoto}`" class="w-full h-auto rounded" alt="Foto Kondisi Unit">
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
                isUnitsModalOpen: false,
                selectedItem: null,
                isPhotoModalOpen: false,
                selectedPhoto: null,

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
            };
        }
    </script>
    @endpush
</x-admin-layout>
