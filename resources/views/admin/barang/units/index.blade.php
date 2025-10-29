<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.barang.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Unit - {{ $barang->nama_barang }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $barang->kode_barang }}</p>
            </div>
        </div>
    </x-slot>

    {{-- Logika Alpine.js untuk mengelola state modal --}}
    <div x-data="{
            showAddModal: @if($errors->any() && old('jumlah_unit')) true @else false @endif,
            showEditModal: false,
            showDeleteModal: false,
            editItem: {},
            deleteItem: {},
            deleteAction: '',
         }"
         @keydown.escape.window="showAddModal = false; showEditModal = false; showDeleteModal = false">

        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p class="font-bold">Sukses</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p class="font-bold">Terjadi Kesalahan</p>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Card Statistik --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-gray-500">
                <p class="text-gray-600 text-sm">Total Unit</p>
                <p class="text-2xl font-bold text-gray-800">{{ $barang->total_stok }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-green-500">
                <p class="text-gray-600 text-sm">Baik</p>
                <p class="text-2xl font-bold text-green-600">{{ $barang->stok_baik }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-blue-500">
                <p class="text-gray-600 text-sm">Dipinjam</p>
                <p class="text-2xl font-bold text-blue-600">{{ $barang->stok_dipinjam }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-yellow-500">
                <p class="text-gray-600 text-sm">Rusak</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $barang->stok_rusak }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-red-500">
                <p class="text-gray-600 text-sm">Hilang</p>
                <p class="text-2xl font-bold text-red-600">{{ $barang->stok_hilang }}</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm">
            {{-- Header Filter dan Pencarian --}}
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <h3 class="text-lg font-semibold text-gray-800">Daftar Unit</h3>
                    <button @click="showAddModal = true" class="flex items-center gap-2 px-4 py-2 bg-green-600 text-sm font-semibold text-white rounded-lg hover:bg-green-700">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Tambah Unit
                    </button>
                </div>

                {{-- Form untuk Filter dan Pencarian --}}
                <form action="{{ route('admin.barang.units.index', $barang) }}" method="GET" class="flex items-center gap-x-2 w-full md:w-auto">

                    <select name="status" onchange="this.form.submit()" class="form-select-custom border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Semua Status</option>
                        <option value="baik" @selected(request('status') == 'baik')>Baik</option>
                        <option value="dipinjam" @selected(request('status') == 'dipinjam')>Dipinjam</option>
                        <option value="rusak" @selected(request('status') == 'rusak')>Rusak</option>
                        <option value="hilang" @selected(request('status') == 'hilang')>Hilang</option>
                    </select>

                    <div class="relative flex-1">
                        <input type="text" name="search" placeholder="Cari kode unit..." value="{{ $search ?? '' }}" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <button type="submit" class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 hover:text-gray-600">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tabel Units --}}
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="p-4 w-16 font-semibold">No.</th>
                            <th class="p-4 font-semibold">Kode Unit</th>
                            <th class="p-4 font-semibold">Status</th>
                            <th class="p-4 font-semibold">Keterangan</th>
                            <th class="p-4 font-semibold">Terakhir Update</th>
                            <th class="p-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($units as $unit)
                            <tr>
                                <td class="p-4 text-center text-gray-500">{{ $loop->iteration + $units->firstItem() - 1 }}</td>
                                <td class="p-4 text-gray-800 font-medium">{{ $unit->unit_code }}</td>
                                <td class="p-4">
                                    @if($unit->status == 'baik')
                                        <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Baik</span>
                                    @elseif($unit->status == 'dipinjam')
                                        <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">Dipinjam</span>
                                    @elseif($unit->status == 'rusak')
                                        <span class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full">Rusak</span>
                                    @elseif($unit->status == 'hilang')
                                        <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Hilang</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded-full">{{ ucfirst($unit->status) }}</span>
                                    @endif
                                </td>
                                <td class="p-4 text-gray-500">{{ $unit->keterangan ?? '-' }}</td>
                                <td class="p-4 text-gray-500">{{ $unit->updated_at->format('d M Y H:i') }}</td>
                                <td class="p-4">
                                    <div class="flex gap-2">
                                        <button @click="showEditModal = true; editItem = {{ json_encode($unit) }}" class="p-2 bg-blue-600 text-white rounded-md hover:bg-blue-700" title="Edit Status">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                        </button>
                                        <button @click="showDeleteModal = true; deleteItem = {{ json_encode($unit) }}; deleteAction = '/admin/barang-units/' + {{ $unit->id }}" class="p-2 bg-red-500 text-white rounded-md hover:bg-red-600" title="Hapus Unit">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-4 text-center text-gray-500">
                                @if ($search || $status)
                                    Tidak ada unit yang cocok dengan filter atau pencarian Anda.
                                @else
                                    Tidak ada data unit.
                                @endif
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Link Paginasi --}}
            <div class="mt-6">
                {{ $units->links() }}
            </div>
        </div>

        {{-- Modal untuk Tambah Unit --}}
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div @click.away="showAddModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 mx-4">
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-lg font-semibold">Tambah Unit Barang</h3>
                    <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form action="{{ route('admin.barang.units.store', $barang) }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <p class="text-sm text-gray-700"><strong>Barang:</strong> {{ $barang->nama_barang }}</p>
                        <p class="text-sm text-gray-700 mt-1"><strong>Kode:</strong> {{ $barang->kode_barang }}</p>
                        <p class="text-sm text-gray-700 mt-1"><strong>Stok Saat Ini:</strong> {{ $barang->stok }} unit</p>
                    </div>
                    <div>
                        <label for="jumlah_unit" class="block text-sm font-medium text-gray-700">Jumlah Unit yang Ditambahkan <span class="text-red-500">*</span></label>
                        <input type="number" name="jumlah_unit" id="jumlah_unit" min="1" max="100" value="{{ old('jumlah_unit', 1) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" required>
                        <p class="mt-1 text-xs text-gray-500">Maksimal 100 unit sekaligus. Unit akan dibuat dengan kode otomatis.</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">
                            <svg class="h-4 w-4 inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                            <strong>Info:</strong> Unit baru akan dibuat dengan status "Baik" dan nomor urut melanjutkan dari unit terakhir.
                        </p>
                    </div>
                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Tambah Unit</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal untuk Edit Status Unit --}}
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div @click.away="showEditModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 mx-4">
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-lg font-semibold">Edit Status Unit</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form :action="'/admin/barang-units/' + editItem.id" method="POST" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kode Unit</label>
                        <p class="text-gray-900 font-semibold" x-text="editItem.unit_code"></p>
                    </div>
                    <div>
                        <label for="edit_status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="edit_status" x-model="editItem.status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="baik">Baik</option>
                            <option value="rusak">Rusak</option>
                            <option value="hilang">Hilang</option>
                            <option value="dipinjam">Dipinjam</option>
                        </select>
                    </div>
                    <div>
                        <label for="edit_keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                        <textarea name="keterangan" id="edit_keterangan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" x-model="editItem.keterangan" placeholder="Keterangan jika rusak atau hilang..."></textarea>
                    </div>
                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal untuk Konfirmasi Hapus --}}
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div @click.away="showDeleteModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 mx-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Hapus Unit?</h3>
                        <p class="text-sm text-gray-600 mt-1">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <p class="text-sm text-gray-700"><strong>Kode Unit:</strong> <span x-text="deleteItem.unit_code"></span></p>
                    <p class="text-sm text-gray-700 mt-1"><strong>Status:</strong> <span x-text="deleteItem.status"></span></p>
                </div>
                <p class="text-sm text-gray-600 mb-4">Apakah Anda yakin ingin menghapus unit ini? Stok barang akan dikurangi 1.</p>
                <form :action="deleteAction" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showDeleteModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
