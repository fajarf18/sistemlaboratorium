{{-- Header Atas (Navbar) --}}
<header class="flex items-center justify-between border-b border-gray-200 bg-white px-4 py-4 lg:px-6">
    <div class="flex items-center">
        {{-- Tombol Toggle Sidebar --}}
        <button x-on:click="sidebarOpen = !sidebarOpen" class="mr-3 text-gray-500 focus:outline-none">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 6H20M4 12H20M4 18H14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </button>
        
        {{-- Judul Header (Responsif) --}}
        <h1 class="hidden text-xl font-semibold text-gray-800 sm:block">@yield('header')</h1>
    </div>

    {{-- Elemen Sebelah Kanan Navbar --}}
    <div class="flex items-center gap-x-5">
        
        {{-- IKON KERANJANG BARU --}}
        <a href="{{ route('user.keranjang.index') }}" class="relative text-gray-500 hover:text-gray-700">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c.51 0 .962-.344 1.084-.845l1.956-7.146A.75.75 0 0020.25 6h-15.75" />
            </svg>
            {{-- Badge Notifikasi Jumlah Barang di Keranjang --}}
            @if(auth()->user() && auth()->user()->keranjangs->count() > 0)
                <span class="absolute -top-2 -right-2 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white">{{ auth()->user()->keranjangs->count() }}</span>
            @endif
        </a>

        {{-- Dropdown Profil Pengguna --}}
        <div x-data="{ dropdownOpen: false }" class="relative">
            <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2">
                <img class="h-9 w-9 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nama) }}&color=7F9CF5&background=EBF4FF" alt="User avatar">
                
                <div class="hidden text-left md:block">
                    <p class="font-semibold text-sm text-gray-800">{{ auth()->user()->nama }}</p>
                    <p class="text-xs text-gray-500">{{ auth()->user()->nim }}</p>
                </div>
            </button>

            <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" x-transition class="absolute right-0 z-10 mt-2 w-48 rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5" style="display: none;">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                
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