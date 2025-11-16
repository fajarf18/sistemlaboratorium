@extends('layouts.app')

@section('header', 'Kembalikan Barang')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div x-data="kembalikanBarangData({{ $detailPeminjamans }})">

    {{-- Notifikasi --}}
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-r-lg shadow-md" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-r-lg shadow-md" role="alert">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="font-bold">Terjadi Kesalahan</p>
            </div>
            <ul class="ml-7 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Info Header & Progress --}}
    <template x-if="details.length > 0">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 mb-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">Informasi Peminjaman</h2>
                        <p class="text-blue-100 text-sm">Silakan cek kondisi setiap unit sebelum mengembalikan</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="bg-white bg-opacity-10 rounded-lg p-3">
                    <p class="text-blue-100 text-xs mb-1">Tanggal Pinjam</p>
                    <p class="font-semibold text-lg" x-text="tanggalPinjamFormatted"></p>
                </div>
                <div class="bg-white bg-opacity-10 rounded-lg p-3">
                    <p class="text-blue-100 text-xs mb-1">Tanggal Wajib Kembali</p>
                    <p class="font-semibold text-lg" x-text="new Date(tanggalWajibKembali).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })"></p>
                </div>
                <div class="bg-white bg-opacity-10 rounded-lg p-3">
                    <p class="text-blue-100 text-xs mb-1">Total Unit Dipinjam</p>
                    <p class="font-semibold text-lg" x-text="allUnits.length + ' unit'"></p>
                </div>
            </div>

                <button @click="cekBarang" :disabled="allUnits.length === 0"
                        class="px-4 md:px-6 py-2.5 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition shadow-lg disabled:bg-gray-300 disabled:text-gray-500 flex items-center justify-center gap-2 w-full md:w-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="hidden sm:inline">Kembalikan Barang</span>
                    <span class="sm:hidden">Kembalikan</span>
                </button>
            </div>
        </div>
    </template>

    {{-- Cards Unit Per Barang --}}
    <div class="space-y-5">
        <template x-for="(detail, index) in details" :key="detail.id">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                {{-- Card Header --}}
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-100 text-blue-600 rounded-full w-10 h-10 flex items-center justify-center font-bold">
                                <span x-text="index + 1"></span>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-800" x-text="detail.barang.nama_barang"></h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full" x-text="detail.barang.tipe"></span>
                                    <span class="text-sm text-gray-600" x-text="`${detail.peminjaman_units.length} unit`"></span>
                                </div>
                            </div>
                        </div>
                        {{-- Mini Stats --}}
                        <div class="flex gap-2 flex-wrap mt-3 md:mt-0">
                            <div class="text-center px-2 py-1 bg-green-50 rounded-lg flex-1 md:flex-none min-w-[70px]">
                                <p class="text-xs text-gray-600">Baik</p>
                                <p class="text-base md:text-lg font-bold text-green-600" x-text="getStatusCountForDetail(detail, 'dikembalikan')"></p>
                            </div>
                            <div class="text-center px-2 py-1 bg-yellow-50 rounded-lg flex-1 md:flex-none min-w-[90px]">
                                <p class="text-xs text-gray-600">Rusak Ringan</p>
                                <p class="text-base md:text-lg font-bold text-yellow-600" x-text="getStatusCountForDetail(detail, 'rusak_ringan')"></p>
                            </div>
                            <div class="text-center px-2 py-1 bg-yellow-100 rounded-lg flex-1 md:flex-none min-w-[90px]">
                                <p class="text-xs text-gray-600">Rusak Berat</p>
                                <p class="text-base md:text-lg font-bold text-yellow-800" x-text="getStatusCountForDetail(detail, 'rusak_berat')"></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Body - Table (Desktop) & Cards (Mobile) --}}
                <div class="p-4 md:p-6">
                    {{-- Desktop Table View --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b-2 border-gray-200">
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode Unit</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keterangan</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Foto Kondisi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="unit in detail.peminjaman_units" :key="unit.id">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-4">
                                            <span class="font-mono text-sm font-semibold text-gray-700" x-text="unit.barang_unit.unit_code"></span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <select x-model="unitStatuses[unit.id].status"
                                                    class="text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all w-full"
                                                    :class="{
                                                        'bg-green-50 text-green-700 border-green-300': unitStatuses[unit.id].status === 'dikembalikan',
                                                        'bg-yellow-50 text-yellow-700 border-yellow-300': unitStatuses[unit.id].status === 'rusak_ringan',
                                                        'bg-yellow-100 text-yellow-800 border-yellow-300': unitStatuses[unit.id].status === 'rusak_berat'
                                                    }">
                                                <option value="dikembalikan">✓ Dikembalikan Baik</option>
                                                <option value="rusak_ringan">⚠ Rusak Ringan</option>
                                                <option value="rusak_berat">⚠ Rusak Berat</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-4">
                                            <template x-if="unitStatuses[unit.id].status !== 'dikembalikan'">
                                                <input type="text"
                                                       x-model="unitStatuses[unit.id].keterangan"
                                                       placeholder="Jelaskan kondisi barang..."
                                                       class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3 py-2">
                                            </template>
                                            <template x-if="unitStatuses[unit.id].status === 'dikembalikan'">
                                                <span class="text-gray-400 text-sm">-</span>
                                            </template>
                                        </td>
                                        <td class="px-4 py-4">
                                            <template x-if="unitStatuses[unit.id].status !== 'dikembalikan'">
                                                <div>
                                                    <label class="cursor-pointer">
                                                        <div class="flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            </svg>
                                                            <span x-text="unitStatuses[unit.id].foto ? 'Ganti Foto' : 'Upload Foto'"></span>
                                                        </div>
                                                        <input type="file" @change="handleUnitPhoto($event, unit.id)" accept="image/*" class="hidden">
                                                    </label>
                                                    <template x-if="unitStatuses[unit.id].foto">
                                                        <div class="mt-2 relative inline-block">
                                                            <img :src="unitStatuses[unit.id].foto" class="w-24 h-24 object-cover rounded-lg border-2 border-gray-200">
                                                            <button @click="unitStatuses[unit.id].foto = null"
                                                                    type="button"
                                                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                            <template x-if="unitStatuses[unit.id].status === 'dikembalikan'">
                                                <span class="text-gray-400 text-sm">-</span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Card View --}}
                    <div class="md:hidden space-y-3">
                        <template x-for="unit in detail.peminjaman_units" :key="unit.id">
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                {{-- Kode Unit --}}
                                <div class="flex items-center justify-between mb-3 pb-3 border-b border-gray-200">
                                    <span class="text-xs font-semibold text-gray-500 uppercase">Kode Unit</span>
                                    <span class="font-mono text-sm font-bold text-gray-700" x-text="unit.barang_unit.unit_code"></span>
                                </div>

                                {{-- Status --}}
                                <div class="mb-3">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Status</label>
                                    <select x-model="unitStatuses[unit.id].status"
                                            class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                            :class="{
                                                'bg-green-50 text-green-700 border-green-300': unitStatuses[unit.id].status === 'dikembalikan',
                                                'bg-yellow-50 text-yellow-700 border-yellow-300': unitStatuses[unit.id].status === 'rusak_ringan',
                                                'bg-yellow-100 text-yellow-800 border-yellow-300': unitStatuses[unit.id].status === 'rusak_berat'
                                            }">
                                        <option value="dikembalikan">✓ Dikembalikan Baik</option>
                                        <option value="rusak_ringan">⚠ Rusak Ringan</option>
                                        <option value="rusak_berat">⚠ Rusak Berat</option>
                                    </select>
                                </div>

                                {{-- Keterangan --}}
                                <template x-if="unitStatuses[unit.id].status !== 'dikembalikan'">
                                    <div class="mb-3">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Keterangan</label>
                                        <input type="text"
                                               x-model="unitStatuses[unit.id].keterangan"
                                               placeholder="Jelaskan kondisi barang..."
                                               class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3 py-2">
                                    </div>
                                </template>

                                {{-- Foto Kondisi --}}
                                <template x-if="unitStatuses[unit.id].status !== 'dikembalikan'">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Foto Kondisi</label>
                                        <label class="cursor-pointer">
                                            <div class="flex items-center justify-center gap-2 text-sm text-blue-600 hover:text-blue-700 bg-blue-50 border-2 border-dashed border-blue-300 rounded-lg py-3 px-4">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span x-text="unitStatuses[unit.id].foto ? 'Ganti Foto' : 'Upload Foto'"></span>
                                            </div>
                                            <input type="file" @change="handleUnitPhoto($event, unit.id)" accept="image/*" class="hidden">
                                        </label>
                                        <template x-if="unitStatuses[unit.id].foto">
                                            <div class="mt-3 relative inline-block">
                                                <img :src="unitStatuses[unit.id].foto" class="w-32 h-32 object-cover rounded-lg border-2 border-gray-300 shadow-sm">
                                                <button @click="unitStatuses[unit.id].foto = null"
                                                        type="button"
                                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-7 h-7 flex items-center justify-center hover:bg-red-600 shadow-md">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

    @if($detailPeminjamans->isEmpty())
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Barang yang Perlu Dikembalikan</h3>
            <p class="text-gray-500">Anda belum memiliki barang yang sedang dipinjam saat ini.</p>
        </div>
    @endif

    <!-- Modal Konfirmasi -->
    <div x-show="showModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 p-2 sm:p-4"
         style="display: none;"
         @keydown.escape.window="showModal = false">
        <div @click.outside="showModal = false" class="bg-white rounded-xl sm:rounded-2xl shadow-2xl w-full max-w-3xl max-h-[95vh] sm:max-h-[90vh] overflow-y-auto">
            <form action="{{ route('user.kembalikan.konfirmasi') }}" method="POST" enctype="multipart/form-data" @submit="isProcessing = true">
            @csrf
            <input type="hidden" name="unit_statuses" :value="JSON.stringify(unitStatuses)">

                {{-- Modal Header --}}
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-4 sm:p-6 rounded-t-xl sm:rounded-t-2xl sticky top-0 z-10">
                    <div class="flex items-center justify-between text-white">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="bg-white bg-opacity-20 p-2 rounded-lg hidden sm:block">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg sm:text-xl font-bold">Konfirmasi Pengembalian</h3>
                                <p class="text-blue-100 text-xs sm:text-sm hidden sm:block">Periksa kembali data sebelum mengirim</p>
                            </div>
                        </div>
                        <button type="button" @click="showModal = false" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-1.5 sm:p-2 transition">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="p-4 sm:p-6 space-y-4 sm:space-y-5">
                    {{-- Alerts --}}
                    <template x-if="unitsRusak.length > 0">
                        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-yellow-500 p-4 rounded-r-lg shadow-sm">
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="font-bold text-yellow-800">Peringatan: Terdeteksi <span x-text="unitsRusak.length"></span> Unit Rusak!</p>
                                    <ul class="mt-2 space-y-1 text-sm text-yellow-700">
                                        <template x-for="unit in unitsRusak" :key="unit.id">
                                            <li class="flex items-center">
                                                <span class="inline-block w-2 h-2 bg-yellow-600 rounded-full mr-2"></span>
                                                <span class="font-mono text-xs" x-text="unit.code"></span>
                                                <span class="mx-2">-</span>
                                                <span class="font-semibold uppercase" x-text="unit.status"></span>
                                                <template x-if="unit.keterangan">
                                                    <span class="ml-2 text-gray-600" x-text="`(${unit.keterangan})`"></span>
                                                </template>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="hariTerlambat > 0">
                        <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <p class="font-bold text-red-800">Keterlambatan: <span x-text="hariTerlambat"></span> Hari!</p>
                            </div>
                        </div>
                    </template>

                    {{-- Ringkasan Statistics --}}
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg sm:rounded-xl p-4 sm:p-5 border border-gray-200">
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-sm sm:text-base">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Ringkasan Status Unit
                        </h4>
                        <div class="grid grid-cols-3 gap-2 sm:gap-3">
                            <div class="bg-gradient-to-br from-green-50 to-green-100 p-3 sm:p-4 rounded-lg sm:rounded-xl border border-green-200 text-center hover:shadow-md transition">
                                <div class="flex justify-center mb-1 sm:mb-2">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-xs text-gray-600 font-medium mb-1">Dikembalikan</p>
                                <p class="text-xl sm:text-2xl font-bold text-green-600" x-text="unitsDikembalikan"></p>
                            </div>
                            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-3 sm:p-4 rounded-lg sm:rounded-xl border border-yellow-200 text-center hover:shadow-md transition">
                                <div class="flex justify-center mb-1 sm:mb-2">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <p class="text-xs text-gray-600 font-medium mb-1">Rusak Ringan</p>
                                <p class="text-xl sm:text-2xl font-bold text-yellow-600" x-text="unitsRusakRingan"></p>
                            </div>
                            <div class="bg-gradient-to-br from-yellow-100 to-yellow-200 p-3 sm:p-4 rounded-lg sm:rounded-xl border border-yellow-300 text-center hover:shadow-md transition">
                                <div class="flex justify-center mb-1 sm:mb-2">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-yellow-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <p class="text-xs text-gray-600 font-medium mb-1">Rusak Berat</p>
                                <p class="text-xl sm:text-2xl font-bold text-yellow-800" x-text="unitsRusakBerat"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Form Fields --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Tanggal Pinjam
                            </label>
                            <input type="text" :value="tanggalPinjamFormatted" disabled class="w-full rounded-lg border-gray-300 bg-gray-50 text-gray-600 font-medium px-4 py-2.5">
                        </div>
                        <div>
                            <label for="tanggal_kembali" class="block text-sm font-semibold text-gray-700 mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Tanggal Dikembalikan <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="tanggal_kembali" name="tanggal_kembali" required :min="tanggalPinjam" x-model="tanggalKembali" @change="cekKeterlambatan" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 px-4 py-2.5 transition">
                        </div>
                    </div>

                    <div>
                        <label for="gambar_bukti" class="block text-sm font-semibold text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Foto Bukti Pengembalian
                        </label>
                        <input type="file" id="gambar_bukti" name="gambar_bukti" accept="image/*" class="w-full text-sm text-gray-600 file:mr-4 file:py-2.5 file:px-5 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 file:transition file:cursor-pointer border border-gray-300 rounded-lg cursor-pointer">
                        <p class="text-xs text-gray-500 mt-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Upload foto kondisi barang yang dikembalikan
                        </p>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:py-4 rounded-b-xl sm:rounded-b-2xl flex flex-col sm:flex-row justify-end gap-2 sm:gap-3 border-t border-gray-200 sticky bottom-0">
                    <button type="button" @click="showModal = false" class="px-4 sm:px-5 py-2.5 bg-white border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition flex items-center justify-center gap-2 order-2 sm:order-1">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Batal
                    </button>
                    <button type="submit"
                        :disabled="isProcessing"
                        class="px-4 sm:px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg shadow-lg flex items-center justify-center gap-2 transition-all order-1 sm:order-2"
                        :class="{'opacity-50 cursor-not-allowed': isProcessing, 'hover:from-blue-700 hover:to-blue-800 hover:shadow-xl': !isProcessing}">
                        <template x-if="!isProcessing">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        <template x-if="isProcessing">
                            <svg class="animate-spin w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span class="text-sm sm:text-base" x-text="isProcessing ? 'Memproses...' : 'Konfirmasi'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function kembalikanBarangData(detailPeminjamans) {
        const details = detailPeminjamans.map(d => ({...d}));
        const allUnits = [];
        const initialStatuses = {};

        // Initialize unit statuses
        details.forEach(detail => {
            detail.peminjaman_units.forEach(unit => {
                allUnits.push(unit);
                initialStatuses[unit.id] = {
                    status: 'dikembalikan',
                    keterangan: '',
                    foto: null
                };
            });
        });

        return {
            details: details,
            allUnits: allUnits,
            unitStatuses: initialStatuses,
            showModal: false,
            tanggalPinjam: details.length > 0 ? details[0].peminjaman.tanggal_pinjam : '',
            tanggalWajibKembali: details.length > 0 ? details[0].peminjaman.tanggal_wajib_kembali : '',
            tanggalKembali: new Date().toLocaleDateString('en-CA'),
            hariTerlambat: 0,
            isProcessing: false,

            get tanggalPinjamFormatted() {
                return this.tanggalPinjam ? new Date(this.tanggalPinjam).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '';
            },

            get unitsDikembalikan() {
                return Object.values(this.unitStatuses).filter(s => s.status === 'dikembalikan').length;
            },

            get unitsRusakRingan() {
                return Object.values(this.unitStatuses).filter(s => s.status === 'rusak_ringan').length;
            },

            get unitsRusakBerat() {
                return Object.values(this.unitStatuses).filter(s => s.status === 'rusak_berat').length;
            },

            get unitsRusak() {
                // Kembalikan daftar objek unit yang rusak (ringan/berat) untuk tampilan peringatan
                return this.allUnits.filter(unit => {
                    const status = this.unitStatuses[unit.id].status;
                    return status === 'rusak_ringan' || status === 'rusak_berat';
                }).map(unit => ({
                    id: unit.id,
                    code: unit.barang_unit.unit_code,
                    status: this.unitStatuses[unit.id].status,
                    keterangan: this.unitStatuses[unit.id].keterangan
                }));
            },

            handleUnitPhoto(event, unitId) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.unitStatuses[unitId].foto = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

            cekKeterlambatan() {
                if (!this.tanggalKembali || !this.tanggalWajibKembali) return;
                const tglKembali = new Date(this.tanggalKembali);
                const tglWajib = new Date(this.tanggalWajibKembali);
                if (tglKembali > tglWajib) {
                    const diffTime = tglKembali - tglWajib;
                    this.hariTerlambat = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                } else {
                    this.hariTerlambat = 0;
                }
            },

            getStatusCountForDetail(detail, status) {
                return detail.peminjaman_units.filter(unit =>
                    this.unitStatuses[unit.id] && this.unitStatuses[unit.id].status === status
                ).length;
            },

            cekBarang() {
                this.cekKeterlambatan();
                this.showModal = true;
            }
        }
    }
</script>
@endsection
