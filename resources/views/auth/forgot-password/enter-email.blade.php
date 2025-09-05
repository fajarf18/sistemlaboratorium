<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Lupa Password</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans">
    <div class="min-h-screen flex flex-col justify-center items-center px-4 bg-gradient-to-br from-[#30BEB6] to-[#3069BE]">
        <div class="w-full max-w-md bg-white p-8 sm:p-10 border rounded-2xl shadow-lg">
            <div class="flex flex-col items-center mb-6">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" class="w-20 h-20 mb-4">
                <h1 class="text-2xl font-bold text-slate-800">Lupa Password</h1>
                <p class="text-sm text-slate-500 mt-1 text-center">Masukkan alamat email Anda yang terdaftar untuk menerima kode verifikasi.</p>
            </div>

            <form method="POST" action="{{ route('password.email.send') }}" class="space-y-4">
                @csrf
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full bg-slate-50" type="email" name="email" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div class="pt-2">
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 border rounded-md font-semibold text-base text-white uppercase bg-gradient-to-r from-[#30BEB6] to-[#3069BE] hover:shadow-lg">
                        Kirim Kode Verifikasi
                    </button>
                </div>
            </form>
             <div class="text-center mt-6">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    Kembali ke Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>