<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Modul Praktikum') }}
        </h2>
    </x-slot>

    <div x-data="{
        items: [{ barang_id: '', jumlah: 1 }],
        addItem() {
            this.items.push({ barang_id: '', jumlah: 1 });
        },
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        }
    }">
        <div class="bg-white p-6 rounded-xl shadow-sm">
            <form action="{{ route('admin.modul.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    {{-- Nama Modul --}}
                    <div>
                        <label for="nama_modul" class="block text-sm font-medium text-gray-700 mb-2">Nama Modul <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_modul" id="nama_modul" value="{{ old('nama_modul') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('nama_modul')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kode Modul --}}
                    <div>
                        <label for="kode_modul" class="block text-sm font-medium text-gray-700 mb-2">Kode Modul <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_modul" id="kode_modul" value="{{ old('kode_modul') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('kode_modul')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status Aktif --}}
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active') ? 'checked' : 'checked' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">Aktif</label>
                    </div>

                    {{-- Daftar Alat --}}
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <label class="block text-sm font-medium text-gray-700">Daftar Alat <span class="text-red-500">*</span></label>
                            <button type="button" @click="addItem()"
                                    class="flex items-center gap-2 px-3 py-1 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Tambah Alat
                            </button>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex gap-3 items-start">
                                    <div class="flex-1">
                                        <select :name="'barang_ids[' + index + ']'"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                required>
                                            <option value="">Pilih Alat</option>
                                            @foreach($barangs as $barang)
                                                <option value="{{ $barang->id }}">{{ $barang->nama_barang }} ({{ $barang->kode }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-24">
                                        <input type="number"
                                               :name="'jumlah[' + index + ']'"
                                               x-model="item.jumlah"
                                               min="1"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               required>
                                    </div>
                                    <button type="button"
                                            @click="removeItem(index)"
                                            x-show="items.length > 1"
                                            class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        @error('barang_ids')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-3 justify-end pt-4">
                        <a href="{{ route('admin.modul.index') }}"
                           class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
