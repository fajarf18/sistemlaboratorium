<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola User') }}
        </h2>
    </x-slot>

    {{-- Tambahkan state untuk modal edit & preview --}}
    <div x-data="{ 
        showEditModal: @if($errors->any()) true @else false @endif,
        showPreviewModal: false,
        editItem: @if($errors->any()) {{ json_encode(old()) }} @else {} @endif,
        previewItem: {}
    }" @keydown.escape.window="showEditModal = false; showPreviewModal = false">

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
            {{-- Header Aksi --}}
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                <form action="{{ route('admin.users.index') }}" method="GET" class="relative w-full md:w-1/3">
                    <input type="text" name="search" placeholder="Cari Pengguna..."
                           value="{{ $search ?? '' }}"
                           class="pl-4 pr-10 py-2 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </button>
                </form>
                <div class="flex items-center gap-2">
                    <button class="flex items-center gap-2 px-4 py-2 border border-blue-600 text-sm font-semibold text-blue-600 rounded-lg hover:bg-blue-50">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        Export
                    </button>
                    <button class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-sm font-semibold text-white rounded-lg hover:bg-blue-700">
                         <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.572a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" /></svg>
                        Filter
                    </button>
                </div>
            </div>

            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Pengguna</h3>
                <a href="#" class="text-sm font-medium text-blue-600 hover:underline">Lihat Semua</a>
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
                                        {{-- Tombol Preview --}}
                                        <button @click="showPreviewModal = true; previewItem = {{ json_encode($user) }}" class="p-2 bg-yellow-400 text-white rounded-md hover:bg-yellow-500">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639l4.43-4.43a1.012 1.012 0 011.43 0l4.43 4.43a1.012 1.012 0 010 1.43l-4.43 4.43a1.012 1.012 0 01-1.43 0l-4.43-4.43zM12.036 12.322a1.012 1.012 0 010-.639l4.43-4.43a1.012 1.012 0 011.43 0l4.43 4.43a1.012 1.012 0 010 1.43l-4.43 4.43a1.012 1.012 0 01-1.43 0l-4.43-4.43z" /></svg>
                                        </button>
                                        <button @click="showEditModal = true; editItem = {{ json_encode($user) }}" class="p-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                        </button>
                                        <button class="p-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-4 text-center text-gray-500">
                                @if ($search)
                                    Tidak ada pengguna yang cocok dengan pencarian "<span class="font-semibold">{{ $search }}</span>".
                                @else
                                    Tidak ada data pengguna.
                                @endif
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>

        {{-- Modal Edit Pengguna --}}
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div @click.away="showEditModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 mx-4">
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-lg font-semibold">Edit Pengguna</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form :action="'/admin/users/' + editItem.id" method="POST" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" :value="editItem.id">
                    <div>
                        <label for="edit_nim" class="block text-sm font-medium text-gray-700">NIM <span class="text-red-500">*</span></label>
                        <input type="text" name="nim" id="edit_nim" x-model="editItem.nim" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="edit_nama" class="block text-sm font-medium text-gray-700">Nama <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" id="edit_nama" x-model="editItem.nama" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="edit_semester" class="block text-sm font-medium text-gray-700">Semester <span class="text-red-500">*</span></label>
                        <input type="number" name="semester" id="edit_semester" x-model="editItem.semester" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
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
                    <div class="flex justify-end gap-4 pt-4 border-t mt-6">
                        <button type="button" @click="showEditModal = false" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Batal</button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Preview Pengguna --}}
        <div x-show="showPreviewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div @click.away="showPreviewModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 mx-4">
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
    </div>
</x-admin-layout>
