<x-dosen-layout>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-8">
                <h2 class="text-2xl font-bold text-gray-800">Dashboard Dosen</h2>

                {{-- Statistik Cards --}}
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    {{-- Total Kelas --}}
                    <div class="rounded-xl bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Kelas</p>
                                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalKelas }}</p>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-sky-100">
                                <svg class="h-6 w-6 text-sky-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Kelas Aktif --}}
                    <div class="rounded-xl bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Kelas Aktif</p>
                                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $kelasAktif }}</p>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-sky-100">
                                <svg class="h-6 w-6 text-sky-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Peminjaman Menunggu Konfirmasi --}}
                    <div class="rounded-xl bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Menunggu Konfirmasi</p>
                                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $peminjamanMenunggu }}</p>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-yellow-100">
                                <svg class="h-6 w-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <a href="{{ route('dosen.kelas-praktikum.create') }}" class="flex flex-col items-center justify-center rounded-xl bg-white p-6 text-center shadow-sm transition hover:bg-sky-50 hover:shadow-md">
                        <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-sky-100 text-sky-500">
                            <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-700">Buat Kelas Praktikum Baru</p>
                    </a>


                </div>

                {{-- Kelas Terbaru --}}
                @if($kelasTerbaru->count() > 0)
                <div class="rounded-xl bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Kelas Terbaru</h3>
                    <div class="space-y-3">
                        @foreach($kelasTerbaru as $kelas)
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <div>
                                <p class="font-medium text-gray-900">{{ $kelas->nama_kelas }}</p>
                                <p class="text-sm text-gray-500">{{ $kelas->mata_kuliah }}</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('dosen.kelas-praktikum.show', $kelas->id) }}" class="rounded-lg bg-sky-100 px-3 py-1.5 text-sm font-medium text-sky-700 hover:bg-sky-200">
                                    Detail
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-dosen-layout>
