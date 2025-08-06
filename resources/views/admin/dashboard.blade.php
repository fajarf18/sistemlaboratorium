<x-admin-layout>
    {{-- Slot untuk header --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    {{-- Isi konten dashboard diletakkan di sini --}}
    <div class="space-y-8">
        {{-- Bagian Header Utama --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <h1 class="text-2xl font-bold text-gray-800">Hello {{ auth()->user()->nama }} 👋</h1>
            <div class="flex items-center space-x-4 mt-4 sm:mt-0">
                <div class="relative">
                    <input type="text" placeholder="Cari" class="pl-10 pr-4 py-2 border border-gray-300 rounded-full w-full sm:w-auto focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                </div>
                <button class="p-2 rounded-full hover:bg-gray-100">
                    <svg class="h-6 w-6 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- List Barang --}}
        <div class="bg-white p-6 rounded-xl shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">List barang</h2>
                <a href="#" class="text-sm font-semibold text-blue-600 hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px] text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="p-4 font-semibold">Nama Barang</th>
                            <th class="p-4 font-semibold">Tipe Barang</th>
                            <th class="p-4 font-semibold">ID Barang</th>
                            <th class="p-4 font-semibold">Quantity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        {{-- Data statis, ganti dengan data dinamis jika perlu --}}
                        <tr>
                            <td class="p-4 text-gray-800">Gunting</td>
                            <td class="p-4 text-gray-500">Tidak Habis Pakai</td>
                            <td class="p-4 text-gray-500">A01</td>
                            <td class="p-4 text-gray-500">1 pcs</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
