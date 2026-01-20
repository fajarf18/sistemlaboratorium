{{-- Latar belakang gelap untuk overlay di mobile --}}
<div x-show="sidebarOpen && window.innerWidth < 1024" class="fixed inset-0 z-20 bg-black bg-opacity-50 transition-opacity lg:hidden" @click="sidebarOpen = false"></div>

{{-- Sidebar Dosen --}}
<aside 
    class="fixed inset-y-0 left-0 z-30 flex w-64 flex-col bg-sky-50 p-4 transition-transform duration-300 border-r border-gray-200 lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    
    {{-- Logo dan Judul --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3 px-4 py-2">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-sky-400 text-white shadow">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" /></svg>
            </div>
            <span class="text-xl font-bold text-gray-800">SIMPPELABS</span>
        </div>
        <button x-on:click="sidebarOpen = false" class="text-gray-500 focus:outline-none lg:hidden">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    {{-- Menu Navigasi --}}
    <nav class="mt-8 flex flex-1 flex-col space-y-1">
        {{-- Link Beranda --}}
        <a href="{{ route('dosen.dashboard') }}" class="{{ request()->routeIs('dosen.dashboard') ? 'bg-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-100' }} flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h7.5" /></svg>
            <span class="font-medium">Beranda</span>
        </a>

        {{-- Link Modul Praktikum --}}
        <a href="{{ route('dosen.modul.index') }}" class="{{ request()->routeIs('dosen.modul.*') ? 'bg-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-100' }} flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
            </svg>
            <span class="font-medium">Modul Praktikum</span>
        </a>

        {{-- Link Kelas Praktikum --}}
        <a href="{{ route('dosen.kelas-praktikum.index') }}" class="{{ request()->routeIs('dosen.kelas-praktikum.*') ? 'bg-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-100' }} flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
            </svg>
            <span class="font-medium">Kelas Praktikum</span>
        </a>


    </nav>
</aside>
