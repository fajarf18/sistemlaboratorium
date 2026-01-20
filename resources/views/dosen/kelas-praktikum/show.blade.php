<x-dosen-layout>
    <div class="py-2">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $kelasPraktikum->nama_kelas }}</h2>
                        <p class="text-gray-600 mt-1">{{ $kelasPraktikum->mata_kuliah }} - {{ $kelasPraktikum->creator->nama }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('dosen.kelas-praktikum.edit', $kelasPraktikum->id) }}" 
                           class="px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700">
                            Edit
                        </a>
                        <a href="{{ route('dosen.kelas-praktikum.index') }}" 
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Kembali
                        </a>
                    </div>
                </div>

                <div class="space-y-6">
                    {{-- Info Kelas --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Deskripsi</p>
                            <p class="font-medium text-gray-900">{{ $kelasPraktikum->deskripsi ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tanggal Praktikum</p>
                            <p class="font-medium text-gray-900">
                                {{ $kelasPraktikum->tanggal_praktikum ? \Carbon\Carbon::parse($kelasPraktikum->tanggal_praktikum)->format('d M Y') : '-' }}
                            </p>
                        </div>
                        @if($kelasPraktikum->jam_mulai && $kelasPraktikum->jam_selesai)
                        <div>
                            <p class="text-sm text-gray-600">Waktu</p>
                            <p class="font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($kelasPraktikum->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($kelasPraktikum->jam_selesai)->format('H:i') }}
                            </p>
                        </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            @if($kelasPraktikum->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Nonaktif</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Jumlah Mahasiswa yang Join</p>
                            <p class="font-medium text-gray-900">{{ $kelasPraktikum->mahasiswa->count() }} Mahasiswa</p>
                        </div>
                    </div>

                    {{-- Daftar Mahasiswa --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Daftar Mahasiswa ({{ $kelasPraktikum->mahasiswa->count() }})</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Mahasiswa</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($kelasPraktikum->mahasiswa as $index => $mhs)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $mhs->nama }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $mhs->email }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada mahasiswa yang join</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Detail Modul --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Modul: {{ $kelasPraktikum->modul->nama_modul }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-600">Kode Modul</p>
                                <p class="font-medium text-gray-900">{{ $kelasPraktikum->modul->kode_modul }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Waktu Modul</p>
                                <p class="font-medium text-gray-900">
                                    @if($kelasPraktikum->modul->jam_mulai && $kelasPraktikum->modul->jam_selesai)
                                        {{ \Carbon\Carbon::parse($kelasPraktikum->modul->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($kelasPraktikum->modul->jam_selesai)->format('H:i') }}
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>

                        <h4 class="font-medium text-gray-700 mb-2">Daftar Alat dalam Modul</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Alat</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($kelasPraktikum->modul->items as $index => $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->barang->nama_barang }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->barang->tipe }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">{{ $item->jumlah }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada alat dalam modul ini</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dosen-layout>
