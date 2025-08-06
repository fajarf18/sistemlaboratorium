<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('List Barang') }}
        </h2>
    </x-slot>

    {{-- Inisialisasi Alpine.js untuk semua modal --}}
    <div x-data="{ 
        showAddModal: false, 
        showEditModal: false,
        showDeleteModal: false, 
        editItem: {},
        itemToDelete: '', 
        deleteAction: ''
    }" @keydown.escape.window="showAddModal = false; showEditModal = false; showDeleteModal = false">
        
        <div class="bg-white p-6 rounded-xl shadow-sm">
            {{-- Header Aksi --}}
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                <div class="relative w-full md:w-1/3">
                    <input type="text" placeholder="Cari Barang" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-center gap-2">
                    <button class="flex items-center gap-2 px-4 py-2 border border-gray-300 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-50">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        Download
                    </button>
                    <button @click="showAddModal = true" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-sm font-semibold text-white rounded-lg hover:bg-blue-700">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Tambah Barang
                    </button>
                    <button class="flex items-center gap-2 px-4 py-2 border border-gray-300 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-50">
                         <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.572a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" /></svg>
                        Filter
                    </button>
                </div>
            </div>
            
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Tabel Data Barang --}}
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="p-4 w-16 font-semibold">No.</th>
                            <th class="p-4 font-semibold">Asset name</th>
                            <th class="p-4 font-semibold">Image</th>
                            <th class="p-4 font-semibold">ID Barang</th>
                            <th class="p-4 font-semibold">Tipe</th>
                            <th class="p-4 font-semibold">Stock</th>
                            <th class="p-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($barangs as $barang)
                            <tr>
                                <td class="p-4 text-center text-gray-500">{{ $loop->iteration }}</td>
                                <td class="p-4 text-gray-800 font-medium">{{ $barang->nama_barang }}</td>
                                <td class="p-4">
                                    <img src="{{ $barang->gambar ? asset('storage/' . $barang->gambar) : 'https://placehold.co/64x48/e2e8f0/334155?text=Gambar' }}" alt="{{ $barang->nama_barang }}" class="w-16 h-12 object-cover rounded-md">
                                </td>
                                <td class="p-4 text-gray-500">{{ $barang->kode_barang }}</td>
                                <td class="p-4 text-gray-500">{{ $barang->tipe }}</td>
                                <td class="p-4 text-gray-500">{{ $barang->stok }} pcs</td>
                                <td class="p-4">
                                    <div class="flex gap-2">
                                        <button class="p-2 bg-yellow-400 text-white rounded-md hover:bg-yellow-500"><svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639l4.43-4.43a1.012 1.012 0 011.43 0l4.43 4.43a1.012 1.012 0 010 1.43l-4.43 4.43a1.012 1.012 0 01-1.43 0l-4.43-4.43zM12.036 12.322a1.012 1.012 0 010-.639l4.43-4.43a1.012 1.012 0 011.43 0l4.43 4.43a1.012 1.012 0 010 1.43l-4.43 4.43a1.012 1.012 0 01-1.43 0l-4.43-4.43z" /></svg></button>
                                        <button @click="showEditModal = true; editItem = @json($barang)" class="p-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                        </button>
                                        <button @click="showDeleteModal = true; itemToDelete = '{{ $barang->nama_barang }}'; deleteAction = '{{ route('admin.barang.destroy', $barang) }}'" class="p-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-4 text-center text-gray-500">Tidak ada data barang.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal untuk Tambah Barang --}}
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div @click.away="showAddModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 mx-4">
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-lg font-semibold">Tambah Barang</h3>
                    <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form action="{{ route('admin.barang.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label for="nama_barang" class="block text-sm font-medium text-gray-700">Nama Barang <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_barang" id="nama_barang" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="tipe" class="block text-sm font-medium text-gray-700">Tipe <span class="text-red-500">*</span></label>
                        <select name="tipe" id="tipe" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Pilih Tipe</option>
                            <option value="Habis Pakai">Habis Pakai</option>
                            <option value="Tidak Habis Pakai">Tidak Habis Pakai</option>
                        </select>
                    </div>
                    <div>
                        <label for="gambar" class="block text-sm font-medium text-gray-700">Image</label>
                        <input type="file" name="gambar" id="gambar" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div>
                        <label for="stok" class="block text-sm font-medium text-gray-700">Stock Barang <span class="text-red-500">*</span></label>
                        <input type="number" name="stok" id="stok" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Tambah</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal untuk Edit Barang --}}
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div @click.away="showEditModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 mx-4">
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-lg font-semibold">Edit Barang</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form :action="`/admin/barang/${editItem.id}`" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="edit_nama_barang" class="block text-sm font-medium text-gray-700">Nama Barang <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_barang" id="edit_nama_barang" x-model="editItem.nama_barang" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="edit_kode_barang" class="block text-sm font-medium text-gray-700">ID Barang <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_barang" id="edit_kode_barang" x-model="editItem.kode_barang" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="edit_tipe" class="block text-sm font-medium text-gray-700">Tipe <span class="text-red-500">*</span></label>
                        <select name="tipe" id="edit_tipe" x-model="editItem.tipe" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="Habis Pakai">Habis Pakai</option>
                            <option value="Tidak Habis Pakai">Tidak Habis Pakai</option>
                        </select>
                    </div>
                    <div>
                        <label for="edit_gambar" class="block text-sm font-medium text-gray-700">Image</label>
                        <input type="file" name="gambar" id="edit_gambar" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <span class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah gambar.</span>
                    </div>
                    <div>
                        <label for="edit_stok" class="block text-sm font-medium text-gray-700">Stock Barang <span class="text-red-500">*</span></label>
                        <input type="number" name="stok" id="edit_stok" x-model="editItem.stok" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="edit_deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="deskripsi" id="edit_deskripsi" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" x-model="editItem.deskripsi"></textarea>
                    </div>
                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Konfirmasi Hapus --}}
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div @click.away="showDeleteModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 mx-4">
                <div class="flex flex-col items-center text-center">
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="h-8 w-8 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                    </div>
                    <h3 class="text-lg font-semibold mt-4">Hapus Barang</h3>
                    <p class="text-gray-600 mt-2">Apakah Anda yakin ingin menghapus barang <strong x-text="itemToDelete"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
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
