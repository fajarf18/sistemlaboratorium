<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }} - Login</title>
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans">
        {{-- 
            PERUBAHAN 1: Latar belakang diubah menjadi gradien.
            Mengganti 'bg-slate-100' dengan class gradien kustom.
            'bg-gradient-to-br' membuat gradien mengalir dari atas kiri ke bawah kanan.
        --}}
        <div class="min-h-screen flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-[#30BEB6] to-[#3069BE]">
            
            {{-- Kartu form tetap putih agar konten mudah dibaca --}}
            <div class="w-full max-w-md bg-white p-8 sm:p-10 border border-slate-200 rounded-2xl shadow-lg">
                
                <div class="flex flex-col items-center mb-6">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Logo STIKes" class="w-20 h-20 mb-4">
                    <h1 class="text-xl font-bold text-slate-800 text-center">Sistem Informasi Manajemen Peminjaman Pengembalian Alat Laboratorium Skills</h1>
                    <p class="text-sm text-slate-500 mt-1">Mohon Login Terlebih Dahulu</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" /></svg>
                        </div>
                        <x-text-input id="nim" type="text" name="nim" :value="old('nim')" required autofocus placeholder="Nomor Induk Mahasiswa (NIM)" class="pl-10 w-full bg-slate-50"/>
                        <x-input-error :messages="$errors->get('nim')" class="mt-2" />
                    </div>

                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                             <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" /></svg>
                        </div>
                        <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Password" class="pl-10 w-full bg-slate-50"/>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between">
        <label for="remember_me" class="inline-flex items-center">
            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
            <span class="ms-2 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm font-medium text-blue-600 hover:text-blue-500" href="{{ route('password.request') }}">
                                {{ __('Lupa Sandi?') }}
                            </a>
                        @endif
                    </div>

                    <div class="pt-2">
                        {{-- 
                            PERUBAHAN 2: Tombol diubah menjadi gradien.
                            Mengganti komponen <x-primary-button> dengan tag <button> biasa
                            agar bisa menerapkan class gradien secara spesifik hanya untuk tombol ini.
                            'hover:shadow-lg hover:shadow-cyan-500/50' memberikan efek glow saat disentuh.
                        --}}
                         <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent rounded-md font-semibold text-base text-white uppercase tracking-widest transition ease-in-out duration-150 bg-gradient-to-r from-[#30BEB6] to-[#3069BE] hover:shadow-lg hover:shadow-cyan-500/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                            {{ __('Login') }}
                        </button>
                    </div>

                    <div class="text-center pt-2">
                        <span class="text-sm font-medium">Belum punya akun? </span><a class="text-sm font-medium text-blue-600 hover:text-blue-500" href="{{ route('register') }}">
                             Daftar di sini
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>