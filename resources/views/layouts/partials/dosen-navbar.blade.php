<header class="sticky top-0 z-10 flex h-16 items-center justify-between bg-white px-4 shadow-sm sm:px-6 lg:px-8">
    <button 
        x-on:click="sidebarOpen = !sidebarOpen"
        class="text-gray-500 focus:outline-none lg:hidden"
    >
        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <div class="flex items-center gap-4">
        <div class="text-sm text-gray-600">
            <span class="font-medium">{{ Auth::user()->nama }}</span>
            <span class="mx-2">•</span>
            <span>Dosen</span>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="rounded-lg px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                Keluar
            </button>
        </form>
    </div>
</header>
