<x-dosen-layout>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="{
                    showDeleteModal: false,
                    itemToDelete: '',
                    deleteAction: ''
                 }"
                 @keydown.escape.window="showDeleteModal = false">

                {{-- Notifikasi --}}
                @if (session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
                        <p class="font-bold">Sukses</p>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
                        <p class="font-bold">Error</p>
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                <div class="bg-white p-6 rounded-xl shadow-sm">
                    {{-- Header --}}
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Kelas Praktikum</h2>
                        <a href="{{ route('dosen.kelas-praktikum.create') }}" class="flex items-center justify-center gap-2 px-4 py-2 bg-sky-600 text-sm font-semibold text-white rounded-lg hover:bg-sky-700">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            Tambah Kelas Praktikum
                        </a>
                    </div>

                    {{-- Tabel --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kelas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Kuliah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Praktikum</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($kelasPraktikums as $index => $kelas)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $kelas->nama_kelas }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $kelas->mata_kuliah }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $kelas->tanggal_praktikum ? \Carbon\Carbon::parse($kelas->tanggal_praktikum)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($kelas->is_active)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex gap-2">
                                                <a href="{{ route('dosen.kelas-praktikum.show', $kelas->id) }}" class="text-blue-600 hover:text-blue-900">Detail</a>
                                                <a href="{{ route('dosen.kelas-praktikum.edit', $kelas->id) }}" class="text-sky-600 hover:text-sky-900">Edit</a>
                                                <button @click="showDeleteModal = true; itemToDelete = '{{ $kelas->nama_kelas }}'; deleteAction = '{{ route('dosen.kelas-praktikum.destroy', $kelas->id) }}'" class="text-red-600 hover:text-red-900">Hapus</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada kelas praktikum</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Modal Hapus --}}
                <div x-show="showDeleteModal" @keydown.escape.window="showDeleteModal = false" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-end justify-center min-h-screen px-4 text-center md:items-center sm:p-0">
                        <div x-cloak @click="showDeleteModal = false" x-show="showDeleteModal" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-40"></div>
                        <div x-cloak x-show="showDeleteModal" class="inline-block w-full max-w-md p-6 my-20 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Hapus Kelas Praktikum</h3>
                            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus kelas praktikum <strong x-text="itemToDelete"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
                            <div class="flex justify-end gap-3">
                                <button @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                                    Batal
                                </button>
                                <form :action="deleteAction" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dosen-layout>
