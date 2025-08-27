{{-- File: resources/views/layouts/partials/admin-navbar.blade.php --}}

<header class="flex items-center justify-between border-b border-gray-200 bg-white px-4 py-4 lg:px-6">
    <div class="flex items-center">
        {{-- Tombol Toggle Sidebar untuk mobile --}}
        <button x-on:click="sidebarOpen = !sidebarOpen" class="mr-3 text-gray-500 focus:outline-none lg:hidden">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 6H20M4 12H20M4 18H14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </button>
        
        {{-- Judul Header Dinamis yang dikirim dari setiap halaman --}}
        <div class="hidden text-xl font-semibold text-gray-800 sm:block">
            {{-- Variabel $header ini akan diterima dari layout utama --}}
            {{ $header ?? 'Dashboard' }}
        </div>
    </div>

    {{-- Elemen Sebelah Kanan Navbar --}}
    <div class="flex items-center gap-x-5">
        
        {{-- IKON NOTIFIKASI BARU --}}
        <a href="{{ route('admin.konfirmasi.index') }}" class="relative text-gray-500 hover:text-gray-700">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
            {{-- Badge Notifikasi (jumlahnya akan kita sediakan dari AppServiceProvider) --}}
            @if(isset($notificationCount) && $notificationCount > 0)
                <span class="absolute -top-2 -right-2 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white">{{ $notificationCount }}</span>
            @endif
        </a>

        {{-- Dropdown Profil Pengguna --}}
        <div x-data="{ dropdownOpen: false }" class="relative">
            <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2">
                <img class="h-9 w-9 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nama) }}&color=7F9CF5&background=EBF4FF" alt="User avatar">
                
                <div class="hidden text-left md:block">
                    <p class="font-semibold text-sm text-gray-800">{{ auth()->user()->nama }}</p>
                    <p class="text-xs text-gray-500">Admin Lab</p>
                </div>
            </button>

            <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" x-transition class="absolute right-0 z-10 mt-2 w-48 rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5" style="display: none;">
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>