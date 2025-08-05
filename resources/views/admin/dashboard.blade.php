@extends('layouts.admin')

@section('header', 'Dashboard')

@section('content')
<div class="space-y-8">
    {{-- Bagian Header Utama --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
        <h1 class="text-2xl font-bold text-gray-800">Hello Mathias 👋</h1>
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
                <thead>
                    <tr class="bg-gray-50 text-gray-600">
                        <th class="p-4 font-semibold">Nama Barang</th>
                        <th class="p-4 font-semibold">Tipe Barang</th>
                        <th class="p-4 font-semibold">ID Barang</th>
                        <th class="p-4 font-semibold">Quantity</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="p-4 text-gray-800">Gunting</td>
                        <td class="p-4 text-gray-500">Tidak Habis Pakai</td>
                        <td class="p-4 text-gray-500">A01</td>
                        <td class="p-4 text-gray-500">1 pcs</td>
                    </tr>
                    <tr>
                        <td class="p-4 text-gray-800">Kapas kassa</td>
                        <td class="p-4 text-gray-500">Habis Pakai</td>
                        <td class="p-4 text-gray-500">A02</td>
                        <td class="p-4 text-gray-500">1 pcs</td>
                    </tr>
                    <tr>
                        <td class="p-4 text-gray-800">Pipet</td>
                        <td class="p-4 text-gray-500">Tidak Habis Pakai</td>
                        <td class="p-4 text-gray-500">A03</td>
                        <td class="p-4 text-gray-500">5 pcs</td>
                    </tr>
                     <tr>
                        <td class="p-4 text-gray-800">Thermo Gun</td>
                        <td class="p-4 text-gray-500">Tidak Habis Pakai</td>
                        <td class="p-4 text-gray-500">A04</td>
                        <td class="p-4 text-gray-500">3 pcs</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- List Barang yang Sedang Dipinjam --}}
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-900">List barang yang sedang dipinjam</h2>
            <a href="#" class="text-sm font-semibold text-blue-600 hover:underline">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] text-sm text-left">
                <thead>
                    <tr class="bg-gray-50 text-gray-600">
                        <th class="p-4 font-semibold">Nama Barang</th>
                        <th class="p-4 font-semibold">ID Barang</th>
                        <th class="p-4 font-semibold">Quantity</th>
                        <th class="p-4 font-semibold">Tanggal dipinjam</th>
                        <th class="p-4 font-semibold">Tanggal dikembalikan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="p-4 text-gray-800">Gunting</td>
                        <td class="p-4 text-gray-500">A01</td>
                        <td class="p-4 text-gray-500">1pcs</td>
                        <td class="p-4 text-gray-500">09/08/2025</td>
                        <td class="p-4 text-gray-500">11/08/2025</td>
                    </tr>
                    <tr>
                        <td class="p-4 text-gray-800">Kapas kassa</td>
                        <td class="p-4 text-gray-500">A02</td>
                        <td class="p-4 text-gray-500">1pcs</td>
                        <td class="p-4 text-gray-500">10/08/2025</td>
                        <td class="p-4 text-gray-500">14/08/2025</td>
                    </tr>
                    <tr>
                        <td class="p-4 text-gray-800">Pipet</td>
                        <td class="p-4 text-gray-500">A03</td>
                        <td class="p-4 text-gray-500">5pcs</td>
                        <td class="p-4 text-gray-500">13/08/2025</td>
                        <td class="p-4 text-gray-500">17/08/2025</td>
                    </tr>
                    <tr>
                        <td class="p-4 text-gray-800">Thermo Gun</td>
                        <td class="p-4 text-gray-500">A04</td>
                        <td class="p-4 text-gray-500">1pcs</td>
                        <td class="p-4 text-gray-500">20/08/2025</td>
                        <td class="p-4 text-gray-500">22/08/2025</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Ringkasan Barang --}}
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Barang</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm flex items-center space-x-4">
                <div class="bg-orange-100 p-3 rounded-lg">
                    <svg class="h-6 w-6 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">105</p>
                    <p class="text-sm text-gray-500">Barang Tersedia</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm flex items-center space-x-4">
                <div class="bg-purple-100 p-3 rounded-lg">
                    <svg class="h-6 w-6 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">8</p>
                    <p class="text-sm text-gray-500">Total barang dipinjam</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection