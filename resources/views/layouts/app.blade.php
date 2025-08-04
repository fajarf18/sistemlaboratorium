<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    {{-- Menggunakan 'defer' agar AlpineJS tidak memblokir render halaman --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body 
    class="font-sans antialiased bg-gray-100"
    x-data="{ sidebarOpen: window.innerWidth >= 1024 }"
    @resize.window="sidebarOpen = window.innerWidth >= 1024"
    {{-- Class ini akan mengunci scroll di body saat sidebar terbuka di mobile --}}
    :class="{ 'overflow-hidden lg:overflow-auto': sidebarOpen && window.innerWidth < 1024 }"
>

    {{-- Sidebar dipanggil di sini. Karena posisinya fixed, dia tidak akan --}}
    {{-- mengganggu elemen lain dan tidak perlu dibungkus div. --}}
    @include('layouts.partials.sidebar')

    {{-- 
        PERUBAHAN UTAMA DI SINI:
        1. Dihilangkan: div pembungkus <div class="flex h-screen">. Ini adalah kunci perbaikan.
        2. Konten utama (div ini) sekarang memiliki margin kiri 'lg:ml-64' yang hanya
           aktif di layar besar untuk memberi ruang bagi sidebar.
        3. Di mobile, margin ini tidak ada, sehingga div ini mengambil lebar penuh
           dan sidebar akan muncul sebagai lapisan di atasnya (overlay) tanpa mendorong konten.
    --}}
    <div class="min-h-screen transition-all duration-300" :class="{ 'lg:ml-64': sidebarOpen }">
        
        @include('layouts.partials.navbar')

        {{-- Main content tidak perlu 'overflow-y-auto' karena body sudah bisa di-scroll --}}
        <main class="p-4 sm:p-6 md:p-8">
            @yield('content')
        </main>

    </div>
</body>
</html>