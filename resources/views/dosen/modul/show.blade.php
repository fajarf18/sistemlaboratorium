<x-dosen-layout>
    <div class="py-2">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-xl shadow-sm mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">{{ $modul->nama_modul }}</h2>
                        <p class="text-sm text-gray-500 mt-1">Kode: <span class="font-mono bg-gray-100 px-2 py-0.5 rounded">{{ $modul->kode_modul }}</span></p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('dosen.modul.edit', $modul->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 text-sm">Edit</a>
                         <form action="{{ route('dosen.modul.destroy', $modul->id) }}" method="POST" onsubmit="return confirm('Hapus modul ini?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">Hapus</button>
                        </form>
                    </div>
                </div>
                
                <div class="prose max-w-none text-gray-700 border-t pt-4">
                    <h3 class="text-lg font-semibold mb-2">Deskripsi</h3>
                    <p class="mb-4">{{ $modul->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
                    

                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Daftar Alat / Bahan</h3>
                @if($modul->items->isEmpty())
                    <p class="text-gray-500 italic">Tidak ada item dalam modul ini.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Alat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($modul->items as $index => $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->barang->nama_barang }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->barang->tipe }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $item->jumlah }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="mt-6">
                <a href="{{ route('dosen.modul.index') }}" class="text-gray-600 hover:text-gray-900 hover:underline">
                    &larr; Kembali ke Daftar Modul
                </a>
            </div>
        </div>
    </div>
</x-dosen-layout>
