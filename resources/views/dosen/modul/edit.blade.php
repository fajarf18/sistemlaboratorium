<x-dosen-layout>
    <div class="py-2">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div x-data="{
                items: {{ json_encode($modul->items->map(function($item) {
                    return ['barang_id' => $item->barang_id, 'jumlah' => $item->jumlah];
                })) }},
                addItem() {
                    this.items.push({ barang_id: '', jumlah: 1 });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                }
            }" class="bg-white p-6 rounded-xl shadow-sm">
                
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Modul: {{ $modul->nama_modul }}</h2>
                
                <form action="{{ route('dosen.modul.update', $modul->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        {{-- Nama Modul --}}
                        <div>
                            <label for="nama_modul" class="block text-sm font-medium text-gray-700 mb-2">Nama Modul <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_modul" id="nama_modul" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required value="{{ old('nama_modul', $modul->nama_modul) }}">
                            @error('nama_modul')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('deskripsi', $modul->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Waktu Praktikum --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="jam_mulai" class="block text-sm font-medium text-gray-700 mb-2">Jam Mulai</label>
                                <input type="time" name="jam_mulai" id="jam_mulai" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="{{ old('jam_mulai', $modul->jam_mulai ? \Carbon\Carbon::parse($modul->jam_mulai)->format('H:i') : '') }}">
                                @error('jam_mulai')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="jam_selesai" class="block text-sm font-medium text-gray-700 mb-2">Jam Selesai</label>
                                <input type="time" name="jam_selesai" id="jam_selesai" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="{{ old('jam_selesai', $modul->jam_selesai ? \Carbon\Carbon::parse($modul->jam_selesai)->format('H:i') : '') }}">
                                @error('jam_selesai')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Daftar Item --}}
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <label class="block text-sm font-medium text-gray-700">Daftar Alat/Bahan <span class="text-red-500">*</span></label>
                                <button type="button" @click="addItem()" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                    + Tambah Alat
                                </button>
                            </div>
                            
                            <div class="space-y-3">
                                <template x-if="items.length === 0">
                                    <div class="text-gray-500 italic text-sm p-3 border border-dashed rounded bg-gray-50 text-center">
                                        Belum ada alat yang ditambahkan. Klik tombol "+ Tambah Alat".
                                    </div>
                                </template>
                                <template x-for="(item, index) in items" :key="index">
                                    <div class="flex gap-3 items-end p-3 border rounded-lg bg-gray-50">
                                        <div class="flex-1">
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Nama Alat</label>
                                            <select :name="'items[' + index + '][barang_id]'" x-model="item.barang_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-blue-500" required>
                                                <option value="">Pilih Alat</option>
                                                @foreach($barangs as $barang)
                                                    <option value="{{ $barang->id }}">{{ $barang->nama_barang }} ({{ $barang->tipe }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="w-24">
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Jumlah</label>
                                            <input type="number" :name="'items[' + index + '][jumlah]'" x-model="item.jumlah" min="1" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-blue-500" required>
                                        </div>
                                        <button type="button" @click="removeItem(index)" class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 mb-[1px]">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                            @error('items')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t">
                            <a href="{{ route('dosen.modul.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Batal</a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-dosen-layout>
