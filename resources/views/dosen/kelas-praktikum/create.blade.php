<x-dosen-layout>
    <div class="py-2">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah Kelas Praktikum</h2>
                
                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('dosen.kelas-praktikum.store') }}" method="POST">
                    @csrf

                    <div class="space-y-6">
                        {{-- Mata Kuliah --}}
                        <div>
                            <label for="mata_kuliah" class="block text-sm font-medium text-gray-700 mb-2">Mata Kuliah <span class="text-red-500">*</span></label>
                            <input type="text" 
                                   name="mata_kuliah" 
                                   id="mata_kuliah"
                                   value="{{ old('mata_kuliah') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                                   placeholder="Contoh: Kimia Dasar, Fisika Dasar, dll"
                                   required>
                            @error('mata_kuliah')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nama Kelas --}}
                        <div>
                            <label for="nama_kelas" class="block text-sm font-medium text-gray-700 mb-2">Nama Kelas <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_kelas" id="nama_kelas" value="{{ old('nama_kelas') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                                   placeholder="Contoh: Praktikum Kimia Dasar - Kelas A"
                                   required>
                            @error('nama_kelas')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Modul Praktikum --}}
                        <div>
                            <label for="modul_id" class="block text-sm font-medium text-gray-700 mb-2">Modul Praktikum <span class="text-red-500">*</span></label>
                            <select name="modul_id" id="modul_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500" required>
                                <option value="">Pilih Modul</option>
                                @foreach($moduls as $modul)
                                    <option value="{{ $modul->id }}" {{ old('modul_id') == $modul->id ? 'selected' : '' }}>
                                        {{ $modul->nama_modul }} - {{ $modul->kode_modul }} ({{ $modul->items->count() }} item)
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Pilih modul yang akan digunakan. Alat dan waktu praktikum akan mengikuti modul ini.</p>
                            @error('modul_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                                      placeholder="Deskripsi kelas praktikum...">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tanggal Praktikum --}}
                        <div>
                            <label for="tanggal_praktikum" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Praktikum</label>
                            <input type="date" name="tanggal_praktikum" id="tanggal_praktikum" value="{{ old('tanggal_praktikum') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            @error('tanggal_praktikum')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Waktu Praktikum --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="jam_mulai" class="block text-sm font-medium text-gray-700 mb-2">Jam Mulai</label>
                                <input type="time" name="jam_mulai" id="jam_mulai" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500" value="{{ old('jam_mulai') }}">
                                @error('jam_mulai')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="jam_selesai" class="block text-sm font-medium text-gray-700 mb-2">Jam Selesai</label>
                                <input type="time" name="jam_selesai" id="jam_selesai" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500" value="{{ old('jam_selesai') }}">
                                @error('jam_selesai')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Status Aktif --}}
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-sky-600 focus:ring-sky-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">Aktif</label>
                        </div>

                        {{-- Tombol Submit --}}
                        <div class="flex gap-3 justify-end pt-4 border-t">
                            <a href="{{ route('dosen.kelas-praktikum.index') }}" 
                               class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 text-sm font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700">
                                Simpan Kelas Praktikum
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-dosen-layout>
