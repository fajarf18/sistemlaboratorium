<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-t">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }} - Register</title>
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans">
        {{-- 
            PERUBAHAN 1: Latar belakang diubah menjadi gradien.
        --}}
        <div class="min-h-screen flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-[#30BEB6] to-[#3069BE]">
            
            {{-- Kartu form diberi shadow yang lebih tebal agar terlihat lebih baik di atas gradien --}}
            <div class="w-full max-w-md bg-white p-8 sm:p-10 border border-slate-200 rounded-2xl shadow-lg">
                
                <div class="flex flex-col items-center mb-6">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Logo STIKes" class="w-20 h-20 mb-4">
                    <h1 class="text-2xl font-bold text-slate-800">Buat Akun Baru</h1>
                    <p class="text-sm text-slate-500 mt-1">Isi data di bawah untuk mendaftar</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" /></svg>
                        </div>
                        <x-text-input id="nim" type="text" name="nim" :value="old('nim')" required placeholder="Nomor Induk Mahasiswa (NIM)" class="pl-10 w-full bg-slate-50"/>
                        <x-input-error :messages="$errors->get('nim')" class="mt-2" />
                    </div>

                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.095a1.23 1.23 0 0 0 .41-1.412A9.992 9.992 0 0 0 10 12a9.992 9.992 0 0 0-6.535 2.493Z" /></svg>
                        </div>
                        <x-text-input id="nama" type="text" name="nama" :value="old('nama')" required placeholder="Nama Lengkap" class="pl-10 w-full bg-slate-50"/>
                        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                    </div>
                    
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a2 2 0 0 0-2 2v1.161l8.441 4.221a1.25 1.25 0 0 0 1.118 0L19 7.162V6a2 2 0 0 0-2-2H3Z" /><path d="M19 8.839l-7.77 3.885a2.75 2.75 0 0 1-2.46 0L1 8.839V14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.839Z" /></svg>
                        </div>
                        <x-text-input id="email" type="email" name="email" :value="old('email')" required placeholder="Alamat Email" class="pl-10 w-full bg-slate-50"/>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <x-text-input id="prodi" type="text" name="prodi" :value="old('prodi')" required placeholder="Prodi" class="w-full bg-slate-50"/>
                            <x-input-error :messages="$errors->get('prodi')" class="mt-2" />
                        </div>
                        <div class="relative">
                            <x-text-input id="semester" type="text" name="semester" :value="old('semester')" required placeholder="Semester" class="w-full bg-slate-50"/>
                            <x-input-error :messages="$errors->get('semester')" class="mt-2" />
                        </div>
                    </div>

                    <div class="relative">
                         <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" /></svg>
                        </div>
                        <x-text-input id="password" type="password" name="password" required placeholder="Buat Password" class="pl-10 w-full bg-slate-50"/>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                           <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" /></svg>
                        </div>
                        <x-text-input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Konfirmasi Password" class="pl-10 w-full bg-slate-50"/>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="pt-2">
                        {{-- 
                            PERUBAHAN 2: Tombol diubah menjadi gradien.
                        --}}
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent rounded-md font-semibold text-base text-white uppercase tracking-widest transition ease-in-out duration-150 bg-gradient-to-r from-[#30BEB6] to-[#3069BE] hover:shadow-lg hover:shadow-cyan-500/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                            {{ __('Daftar Sekarang') }}
                        </button>
                    </div>

                    <div class="text-center pt-2">
                        <span class="text-sm font-medium">Sudah punya akun? </span>
                        <a class="text-sm font-medium text-blue-600 hover:text-blue-500" href="{{ route('login') }}">
                             Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>