{{-- Header Atas (Navbar) --}}
<header class="flex items-center justify-between border-b border-gray-200 bg-white px-4 py-4 lg:px-6">
    <div class="flex items-center">
        {{-- Tombol Toggle Sidebar --}}
        <button x-on:click="sidebarOpen = !sidebarOpen" class="mr-3 text-gray-500 focus:outline-none">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 6H20M4 12H20M4 18H14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </button>
        
        {{-- Judul Header (Responsif: Muncul di layar 'sm' ke atas) --}}
        <h1 class="hidden text-xl font-semibold text-gray-800 sm:block">@yield('header')</h1>
    </div>

    {{-- Dropdown Profil Pengguna --}}
    <div x-data="{ dropdownOpen: false }" class="relative">
        <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2">
            <img class="h-9 w-9 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nama) }}&color=7F9CF5&background=EBF4FF" alt="User avatar">
            
            {{-- Nama & NIM (Responsif: Muncul di layar 'md' ke atas) --}}
            <div class="hidden text-left md:block">
                <p class="font-semibold text-sm text-gray-800">{{ auth()->user()->nama }}</p>
                <p class="text-xs text-gray-500">{{ auth()->user()->nim }}</p>
            </div>
        </button>

        {{-- Menu Dropdown --}}
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
</header>
