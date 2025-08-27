<x-admin-layout>
    {{-- Slot untuk header --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-8">

                {{-- Kartu Aksi Cepat untuk Admin --}}
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    
                    {{-- Kartu Manajemen Barang --}}
                    <a href="{{ route('admin.barang.index') }}" class="flex flex-col items-center justify-center rounded-xl bg-white p-6 text-center shadow-sm transition hover:bg-sky-50 hover:shadow-md">
                        <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-sky-100 text-sky-500">
                            <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-700">Manajemen Barang</p>
                    </a>

                    {{-- Kartu Konfirmasi --}}
                    <a href="{{ route('admin.konfirmasi.index') }}" class="flex flex-col items-center justify-center rounded-xl bg-white p-6 text-center shadow-sm transition hover:bg-sky-50 hover:shadow-md">
                        <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-sky-100 text-sky-500">
                            <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-700">Konfirmasi</p>
                    </a>

                    {{-- Kartu Manajemen Pengguna --}}
                     <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center justify-center rounded-xl bg-white p-6 text-center shadow-sm transition hover:bg-sky-50 hover:shadow-md">
                        <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-sky-100 text-sky-500">
                           <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-4.663M12 3.375c-3.418 0-6.162 2.65-6.162 5.923 0 3.272 2.744 5.923 6.162 5.923s6.162-2.65 6.162-5.923c0-3.272-2.744-5.923-6.162-5.923zM12 12.75a2.625 2.625 0 110-5.25 2.625 2.625 0 010 5.25z" />
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-700">Manajemen Pengguna</p>
                    </a>

                    <a href="{{ route('admin.history.index') }}" class="flex flex-col items-center justify-center rounded-xl bg-white p-6 text-center shadow-sm transition hover:bg-sky-50 hover:shadow-md">
                        <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-sky-100 text-sky-500">
                            <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-700">History Peminjaman</p>
                    </a>
                </div>
                
                {{-- Anda bisa menambahkan ringkasan data lain di sini jika perlu --}}

            </div>
        </div>
    </div>
</x-admin-layout>