<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Barang') }}
        </h2>
    </x-slot>

    {{-- Logika Alpine.js untuk mengelola state modal --}}
    <div x-data="{
            showAddModal: @if($errors->any() && old('form_type') === 'add') true @else false @endif,
            showEditModal: @if($errors->any() && old('form_type') === 'edit') true @else false @endif,
            showDeleteModal: false,
            showPreviewModal: false,
            showImportModal: @if($errors->has('file') || session('import_errors')) true @else false @endif,
            editItem: @if($errors->any() && old('form_type') === 'edit') {{ json_encode(old()) }} @else {} @endif,
            previewItem: {},
            itemToDelete: '',
            deleteAction: '',
            addPreviewUrl: null,
            editPreviewUrl: null,
            importFileName: '',
            handleFileChange(event, type) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        if (type === 'add') this.addPreviewUrl = e.target.result;
                        else if (type === 'edit') this.editPreviewUrl = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },
            handleImportFileChange(event) {
                const file = event.target.files[0];
                if (file) {
                    this.importFileName = file.name;
                }
            },
            resetAddModal() {
                this.showAddModal = false;
                this.addPreviewUrl = null;
                document.getElementById('addBarangForm').reset();
            },
            resetEditModal() {
                this.showEditModal = false;
                this.editPreviewUrl = null;
            },
            resetImportModal() {
                this.showImportModal = false;
                this.importFileName = '';
                const fileInput = document.getElementById('import_file');
                if (fileInput) fileInput.value = '';
            }
         }"
         @keydown.escape.window="resetAddModal(); resetEditModal(); showDeleteModal = false; showPreviewModal = false; resetImportModal()">
        
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
        @if (session('import_errors'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p class="font-bold">Error Import:</p>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach (session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white p-6 rounded-xl shadow-sm">
            {{-- Header Aksi, Filter, dan Pencarian (RESPONSIF & STABIL) --}}
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                
                {{-- Tombol Aksi (Kiri di Desktop, Atas di Mobile) --}}
                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <button @click="showAddModal = true" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-sm font-semibold text-white rounded-lg hover:bg-blue-700">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Tambah
                    </button>
                    <button @click="showImportModal = true" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-sm font-semibold text-white rounded-lg hover:bg-green-700">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                        Import
                    </button>
                    <a href="{{ route('admin.barang.download') }}" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-50">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        Export
                    </a>
                </div>

                {{-- Form untuk Filter dan Pencarian (Kanan di Desktop, Bawah di Mobile) --}}
                <form action="{{ route('admin.barang.index') }}" method="GET" class="flex items-center gap-x-2 w-full md:w-auto">
                    
                    <select name="tipe" onchange="this.form.submit()" class="form-select-custom border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Semua Tipe</option>
                        <option value="Habis Pakai" @selected(request('tipe') == 'Habis Pakai')>Habis Pakai</option>
                        <option value="Tidak Habis Pakai" @selected(request('tipe') == 'Tidak Habis Pakai')>Tidak Habis Pakai</option>
                    </select>
                    
                    <div class="relative flex-1">
                        <input type="text" name="search" placeholder="Cari barang..." value="{{ $search ?? '' }}" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <button type="submit" class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 hover:text-gray-600">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                        </button>
                    </div>
                </form>
            </div>
            
            {{-- Tabel Barang (Hanya bagian ini yang akan scroll jika perlu) --}}
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="p-4 w-16 font-semibold">No.</th>
                            <th class="p-4 font-semibold">Nama Barang</th>
                            <th class="p-4 font-semibold">Gambar</th>
                            <th class="p-4 font-semibold">ID Barang</th>
                            <th class="p-4 font-semibold">Tipe</th>
                            <th class="p-4 font-semibold">Total Unit</th>
                            <th class="p-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($barangs as $barang)
                            <tr>
                                <td class="p-4 text-center text-gray-500">{{ $loop->iteration + $barangs->firstItem() - 1 }}</td>
                                <td class="p-4 text-gray-800 font-medium">{{ $barang->nama_barang }}</td>
                                <td class="p-4">
                                    <img src="{{ Str::startsWith($barang->gambar, 'http') ? $barang->gambar : asset('storage/' . $barang->gambar) }}" alt="{{ $barang->nama_barang }}" class="w-16 h-12 object-cover rounded-md">
                                </td>
                                <td class="p-4 text-gray-500">{{ $barang->kode_barang }}</td>
                                <td class="p-4 text-gray-500">{{ $barang->tipe }}</td>
                                <td class="p-4 text-gray-500">{{ $barang->total_stok }} unit</td>
                                <td class="p-4">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.barang.units.index', $barang) }}" class="p-2 bg-purple-600 text-white rounded-md hover:bg-purple-700" title="Lihat Units">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                                        </a>
                                        <button @click="showPreviewModal = true; previewItem = {{ json_encode($barang) }}" class="p-2 bg-yellow-400 text-white rounded-md hover:bg-yellow-500" title="Lihat Detail">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639l4.43-4.43a1.012 1.012 0 011.43 0l4.43 4.43a1.012 1.012 0 010 1.43l-4.43 4.43a1.012 1.012 0 01-1.43 0l-4.43-4.43zM12.036 12.322a1.012 1.012 0 010-.639l4.43-4.43a1.012 1.012 0 011.43 0l4.43 4.43a1.012 1.012 0 010 1.43l-4.43 4.43a1.012 1.012 0 01-1.43 0l-4.43-4.43z" /></svg>
                                        </button>
                                        <button @click="showEditModal = true; editItem = {{ json_encode($barang) }}" class="p-2 bg-blue-600 text-white rounded-md hover:bg-blue-700" title="Edit Barang">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                        </button>
                                        <button @click="showDeleteModal = true; itemToDelete = '{{ $barang->nama_barang }}'; deleteAction = '{{ route('admin.barang.destroy', $barang) }}'" class="p-2 bg-red-500 text-white rounded-md hover:bg-red-600" title="Hapus Barang">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-4 text-center text-gray-500">
                                @if ($search || $tipe)
                                    Tidak ada barang yang cocok dengan filter atau pencarian Anda.
                                @else
                                    Tidak ada data barang.
                                @endif
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Link Paginasi --}}
            <div class="mt-6">
                {{ $barangs->links() }}
            </div>
        </div>
        {{-- Modal untuk Tambah Barang --}}
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div @click.away="showAddModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-lg font-semibold">Tambah Barang</h3>
                    <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form action="{{ route('admin.barang.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="form_type" value="add">
                    <div>
                        <label for="nama_barang" class="block text-sm font-medium text-gray-700">Nama Barang <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="kode_barang" class="block text-sm font-medium text-gray-700">ID/Kode Barang <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_barang" id="kode_barang" value="{{ old('kode_barang') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm uppercase" required>
                        <p class="mt-1 text-xs text-gray-500">Kode akan digunakan untuk membentuk kode unit (contoh: KMB-001).</p>
                    </div>
                    <div>
                        <label for="tipe" class="block text-sm font-medium text-gray-700">Tipe <span class="text-red-500">*</span></label>
                        <select name="tipe" id="tipe" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Pilih Tipe</option>
                            <option value="Habis Pakai" @selected(old('tipe') == 'Habis Pakai')>Habis Pakai</option>
                            <option value="Tidak Habis Pakai" @selected(old('tipe') == 'Tidak Habis Pakai')>Tidak Habis Pakai</option>
                        </select>
                    </div>
                    <div>
                        <label for="gambar" class="block text-sm font-medium text-gray-700">Image</label>
                        <input type="file" name="gambar" id="gambar" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div>
                        <label for="stok" class="block text-sm font-medium text-gray-700">Stock Barang <span class="text-red-500">*</span></label>
                        <input type="number" name="stok" id="stok" value="{{ old('stok') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('deskripsi') }}</textarea>
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
            <div @click.away="showEditModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-lg font-semibold">Edit Barang</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form :action="'/admin/barang/' + editItem.id" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="form_type" value="edit">
                    <input type="hidden" name="id" :value="editItem.id">
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

        {{-- Modal untuk Preview Barang --}}
        <div x-show="showPreviewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div @click.away="showPreviewModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-lg font-semibold" x-text="'Detail ' + previewItem.nama_barang"></h3>
                    <button @click="showPreviewModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="mt-4 space-y-4">
                    <div class="flex justify-center">
                            <img :src="previewItem.gambar ? (previewItem.gambar.startsWith('http') ? previewItem.gambar : '/storage/' + previewItem.gambar) : 'https://placehold.co/300x200/e2e8f0/334155?text=Gambar'" 
                     :alt="previewItem.nama_barang" 
                     class="w-16 max-w-sm h-auto object-cover rounded-lg">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">ID Barang</p>
                            <p class="font-semibold" x-text="previewItem.kode_barang"></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Tipe</p>
                            <p class="font-semibold" x-text="previewItem.tipe"></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Total Unit</p>
                            <p class="font-semibold" x-text="(previewItem.total_stok ?? 0) + ' unit'"></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Unit Siap Dipinjam (baik)</p>
                            <p class="font-semibold" x-text="previewItem.stok + ' unit'"></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Tanggal Ditambahkan</p>
                            <p class="font-semibold" x-text="new Date(previewItem.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })"></p>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-500">Deskripsi</p>
                        <p class="mt-1" x-text="previewItem.deskripsi || 'Tidak ada deskripsi.'"></p>
                    </div>
                </div>
                <div class="flex justify-end pt-4 mt-4 border-t">
                    <button type="button" @click="showPreviewModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Tutup</button>
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

        {{-- Modal untuk Import --}}
        <div x-show="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div @click.away="showImportModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-2xl p-6 mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-lg font-semibold">Import Data Barang</h3>
                    <button @click="showImportModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                {{-- Instruksi --}}
                <div class="mt-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-blue-500 mr-2 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold">Panduan Import:</p>
                            <ul class="mt-2 list-disc list-inside space-y-1">
                                <li>Download template Excel terlebih dahulu</li>
                                <li>Isi data sesuai format yang ada di template</li>
                                <li>Jangan ubah/hapus header kolom</li>
                                <li>Kolom bertanda * wajib diisi</li>
                                <li>Tipe harus: "Habis Pakai" atau "Tidak Habis Pakai"</li>
                                <li>File maksimal 2MB (format .xlsx atau .xls)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Button Download Template --}}
                <div class="mt-4 flex justify-center">
                    <a href="{{ route('admin.barang.downloadTemplate') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-purple-800 shadow-lg">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        Download Template Excel
                    </a>
                </div>

                <div class="my-6 border-t border-gray-200"></div>

                {{-- Form Upload --}}
                <form action="{{ route('admin.barang.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label for="import_file" class="block text-sm font-medium text-gray-700 mb-2">
                            Upload File Excel <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <div x-show="!importFileName" class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-green-500 transition">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="import_file" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none">
                                            <span>Pilih file Excel</span>
                                            <input id="import_file" name="file" type="file" accept=".xlsx,.xls" class="hidden" required @change="handleImportFileChange($event)">
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500">XLSX atau XLS maksimal 2MB</p>
                                </div>
                            </div>

                            {{-- Preview File Terpilih --}}
                            <div x-show="importFileName" class="flex items-center justify-between px-4 py-3 border-2 border-green-500 bg-green-50 rounded-lg" style="display: none;">
                                <div class="flex items-center gap-3">
                                    <svg class="h-8 w-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800" x-text="importFileName"></p>
                                        <p class="text-xs text-gray-600">File siap untuk diimport</p>
                                    </div>
                                </div>
                                <button type="button" @click="importFileName = ''; document.getElementById('import_file').value = ''" class="text-red-600 hover:text-red-800">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        </div>
                        @error('file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" @click="resetImportModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Batal</button>
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2" :disabled="!importFileName" :class="{'opacity-50 cursor-not-allowed': !importFileName}">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                            Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
