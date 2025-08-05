@extends('layouts.app')

@section('header', 'Profil Pengguna')

@section('content')
<div class="space-y-6">

    {{-- FORM UNTUK INFORMASI PROFIL --}}
    <div class="p-4 sm:p-8 bg-white shadow-sm rounded-xl">
        <div class="max-w-xl">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        Informasi Profil
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Perbarui informasi email dan semester Anda.
                    </p>
                </header>

                <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                    @csrf
                    @method('patch')

                    {{-- NIM (Tidak bisa diubah) --}}
                    <div>
                        <label for="nim" class="block font-medium text-sm text-gray-700">NIM</label>
                        <input id="nim" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100" value="{{ $user->nim }}" disabled />
                    </div>

                    {{-- Nama Lengkap (Tidak bisa diubah) --}}
                    <div>
                        <label for="nama" class="block font-medium text-sm text-gray-700">Nama Lengkap</label>
                        <input id="nama" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100" value="{{ $user->nama }}" disabled />
                    </div>

                    {{-- Email (Bisa diubah) --}}
                    <div>
                        <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                        <input id="email" name="email" type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('email', $user->email) }}" required autocomplete="username" />
                        @if ($errors->get('email'))
                            <p class="text-sm text-red-600 mt-2">{{ $errors->first('email') }}</p>
                        @endif
                    </div>

                    {{-- Prodi (Tidak bisa diubah) --}}
                    <div>
                        <label for="prodi" class="block font-medium text-sm text-gray-700">Prodi</label>
                        <input id="prodi" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100" value="{{ $user->prodi }}" disabled />
                    </div>

                    {{-- Semester (Bisa diubah) --}}
                    <div>
                        <label for="semester" class="block font-medium text-sm text-gray-700">Semester</label>
                        <input id="semester" name="semester" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('semester', $user->semester) }}" required />
                         @if ($errors->get('semester'))
                            <p class="text-sm text-red-600 mt-2">{{ $errors->first('semester') }}</p>
                        @endif
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">Simpan</button>

                        @if (session('status') === 'profile-updated')
                            <p
                                x-data="{ show: true }"
                                x-show="show"
                                x-transition
                                x-init="setTimeout(() => show = false, 2000)"
                                class="text-sm text-gray-600"
                            >Tersimpan.</p>
                        @endif
                    </div>
                </form>
            </section>
        </div>
    </div>

    {{-- FORM UNTUK UPDATE PASSWORD --}}
    <div class="p-4 sm:p-8 bg-white shadow-sm rounded-xl">
        <div class="max-w-xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>
</div>
@endsection