<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Pengguna') }}
        </h2>
    </x-slot>

    <div x-data="{ 
            showEditModal: @if($errors->any()) true @else false @endif,
            showPreviewModal: false,
            showDeleteModal: false,
            editItem: @if($errors->any()) {{ json_encode(old()) }} @else {} @endif,
            previewItem: {},
            itemToDelete: '', 
            deleteAction: ''
         }" 
         @keydown.escape.window="showEditModal = false; showPreviewModal = false; showDeleteModal = false">
        
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

        <div class="bg-white p-6 rounded-xl shadow-sm">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                <a href="{{ route('admin.users.download', request()->query()) }}" class="flex items-center justify-center gap-2 w-full md:w-auto px-4 py-2 border border-gray-300 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-50">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    Download
                </a>
                <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2 w-full md:w-auto">
                    <select name="prodi" onchange="this.form.submit()" class="border-gray-300 rounded-lg shadow-sm w-full sm:w-auto focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Semua Prodi</option>
                        @foreach($prodis as $p)
                            <option value="{{ $p }}" @selected(request('prodi') == $p)>{{ $p }}</option>
                        @endforeach
                    </select>
                    <select name="semester" onchange="this.form.submit()" class="border-gray-300 rounded-lg shadow-sm w-full sm:w-auto focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Semua Semester</option>
                        @foreach($semesters as $s)
                            <option value="{{ $s }}" @selected(request('semester') == $s)>Semester {{ $s }}</option>
                        @endforeach
                    </select>
                    <div class="relative w-full sm:w-auto">
                        <input type="text" name="search" placeholder="Cari pengguna..." value="{{ $search ?? '' }}" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <button type="submit" class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 hover:text-gray-600">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="p-4 font-semibold">NIM</th>
                            <th class="p-4 font-semibold">Nama</th>
                            <th class="p-4 font-semibold">Email</th>
                            <th class="p-4 font-semibold">Prodi</th>
                            <th class="p-4 font-semibold">Semester</th>
                            <th class="p-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($users as $user)
                            <tr>
                                <td class="p-4 text-gray-500">{{ $user->nim ?? '-' }}</td>
                                <td class="p-4 text-gray-800 font-medium">{{ $user->nama }}</td>
                                <td class="p-4 text-gray-500">{{ $user->email }}</td>
                                <td class="p-4 text-gray-500">{{ $user->prodi ?? '-' }}</td>
                                <td class="p-4 text-gray-500">{{ $user->semester ?? '-' }}</td>
                                <td class="p-4">
                                    <div class="flex gap-2">
                                        <button @click="showPreviewModal = true; previewItem = {{ json_encode($user) }}" class="p-2 bg-yellow-400 text-white rounded-md hover:bg-yellow-500" title="Lihat Detail">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639l4.43-4.43a1.012 1.012 0 011.43 0l4.43 4.43a1.012 1.012 0 010 1.43l-4.43 4.43a1.012 1.012 0 01-1.43 0l-4.43-4.43zM12.036 12.322a1.012 1.012 0 010-.639l4.43-4.43a1.012 1.012 0 011.43 0l4.43 4.43a1.012 1.012 0 010 1.43l-4.43 4.43a1.012 1.012 0 01-1.43 0l-4.43-4.43z" /></svg>
                                        </button>
                                        <button @click="showEditModal = true; editItem = {{ json_encode($user) }}" class="p-2 bg-blue-600 text-white rounded-md hover:bg-blue-700" title="Edit Pengguna">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                        </button>
                                        <button @click="showDeleteModal = true; itemToDelete = '{{ $user->nama }}'; deleteAction = '{{ route('admin.users.destroy', $user) }}'" class="p-2 bg-red-500 text-white rounded-md hover:bg-red-600" title="Hapus Pengguna">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-4 text-center text-gray-500">
                                @if ($search || $prodi || $semester)
                                    Tidak ada pengguna yang cocok dengan filter atau pencarian Anda.
                                @else
                                    Tidak ada data pengguna.
                                @endif
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $users->links() }}</div>
        </div>

        {{-- Modal Edit Pengguna --}}
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" style="display: none;">
            <div @click.away="showEditModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center pb-3 border-b sticky top-0 bg-white">
                    <h3 class="text-lg font-semibold">Edit Pengguna</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                <form :action="'/admin/users/' + editItem.id" method="POST" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" :value="editItem.id">
                    
                    <div>
                        <label for="edit_nama" class="block text-sm font-medium text-gray-700">Nama <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" id="edit_nama" x-model="editItem.nama" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="edit_nim" class="block text-sm font-medium text-gray-700">NIM <span class="text-red-500">*</span></label>
                        <input type="text" name="nim" id="edit_nim" x-model="editItem.nim" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="edit_email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="edit_email" x-model="editItem.email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="edit_prodi" class="block text-sm font-medium text-gray-700">Prodi <span class="text-red-500">*</span></label>
                        <select name="prodi" id="edit_prodi" x-model="editItem.prodi" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Pilih Prodi</option>
                            <option value="S1 Keperawatan">S1 Keperawatan</option>
                            <option value="D3 Kebidanan">D3 Kebidanan</option>
                            <option value="S1 Informatika">S1 Informatika</option>
                        </select>
                    </div>
                    <div>
                        <label for="edit_semester" class="block text-sm font-medium text-gray-700">Semester <span class="text-red-500">*</span></label>
                        <input type="number" name="semester" id="edit_semester" x-model="editItem.semester" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <hr>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                        <input type="password" name="password" id="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" autocomplete="new-password">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="flex justify-end gap-4 pt-4 border-t mt-6">
                        <button type="button" @click="showEditModal = false" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Batal</button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Preview Pengguna --}}
        <div x-show="showPreviewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div @click.away="showPreviewModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center pb-3">
                    <h3 class="text-lg font-semibold">Info Pengguna</h3>
                    <button @click="showPreviewModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">NIM <span class="text-red-500">*</span></label>
                        <input type="text" :value="previewItem.nim" class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama <span class="text-red-500">*</span></label>
                        <input type="text" :value="previewItem.nama" class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Semester <span class="text-red-500">*</span></label>
                        <input type="text" :value="previewItem.semester" class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Prodi <span class="text-red-500">*</span></label>
                        <input type="text" :value="previewItem.prodi" class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm" disabled>
                    </div>
                    <div class="flex flex-col items-center pt-4">
                        <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mb-2">
                             <svg class="w-16 h-16 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                        </div>
                        <p class="font-semibold" x-text="previewItem.nama"></p>
                    </div>
                    <div class="flex justify-center pt-4 border-t mt-6">
                        <button type="button" @click="showPreviewModal = false" class="px-8 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Kembali</button>
                    </div>
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
                    <h3 class="text-lg font-semibold mt-4">Hapus Pengguna</h3>
                    <p class="text-gray-600 mt-2">Apakah Anda yakin ingin menghapus pengguna <strong x-text="itemToDelete"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
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
    </div>
</x-admin-layout>
