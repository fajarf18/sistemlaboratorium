{{-- Latar belakang gelap untuk overlay di mobile --}}
<div x-show="sidebarOpen" class="fixed inset-0 z-20 bg-black bg-opacity-50 transition-opacity lg:hidden" @click="sidebarOpen = false"></div>

{{-- Sidebar Admin --}}
<aside 
    class="fixed inset-y-0 left-0 z-30 flex w-64 flex-col bg-sky-50 p-4 transition-transform duration-300 border-r border-gray-200 lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    
    {{-- Logo dan Judul --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3 px-4 py-2">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-sky-400 text-white shadow">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" /></svg>
            </div>
            <span class="text-xl font-bold text-gray-800">Inventory</span>
        </div>
        <button x-on:click="sidebarOpen = false" class="text-gray-500 focus:outline-none lg:hidden">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    {{-- Menu Navigasi --}}
    <nav class="mt-8 flex flex-1 flex-col space-y-1">
        {{-- Link Beranda --}}
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'bg-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-100' }} flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h7.5" /></svg>
            <span class="font-medium">Beranda</span>
        </a>

        {{-- Link Barang --}}
        <a href="{{ route('admin.barang.index') }}" class="{{ request()->routeIs('admin.barang.index') ? 'bg-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-100' }} flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>
            <span class="font-medium">Barang</span>
        </a>

        {{-- Link Modul --}}
        <a href="{{ route('admin.modul.index') }}" class="{{ request()->routeIs('admin.modul.*') ? 'bg-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-100' }} flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
            </svg>
            <span class="font-medium">Modul</span>
        </a>

        {{-- Link Dosen Pengampu --}}
        <a href="{{ route('admin.dosen-pengampu.index') }}" class="{{ request()->routeIs('admin.dosen-pengampu.*') ? 'bg-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-100' }} flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
            </svg>
            <span class="font-medium">Dosen Pengampu</span>
        </a>

        {{-- PERUBAHAN DI SINI: Ikon Persetujuan --}}
        <a href="{{ route('admin.konfirmasi.index') }}" class="{{ request()->routeIs('admin.konfirmasi.index') ? 'bg-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-100' }} flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">Persetujuan</span>
        </a>
         <a href="{{ route('admin.status.index') }}" class="{{ request()->routeIs('admin.status.index') ? 'bg-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-100' }} flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.963A3.426 3.426 0 0010.5 6.57a4.5 4.5 0 018.337 2.083v.357a4.5 4.5 0 01-8.337 2.083m-7.5-2.963A3.426 3.426 0 003 6.57a4.5 4.5 0 018.337 2.083v.357a4.5 4.5 0 01-8.337-2.083zM3 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m5.963 2.72a3.426 3.426 0 01-3.163 2.083 4.5 4.5 0 01-8.337-2.083v-.357a4.5 4.5 0 018.337-2.083m5.963 2.72a3.426 3.426 0 013.163 2.083 4.5 4.5 0 01-8.337-2.083v-.357a4.5 4.5 0 018.337-2.083z" />
            </svg>
            <span class="font-medium">Status Pengguna</span>
        </a>
        {{-- PERUBAHAN DI SINI: Ikon Pengguna --}}
        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.index') ? 'bg-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-100' }} flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.963A3.426 3.426 0 0010.5 6.57a4.5 4.5 0 018.337 2.083v.357a4.5 4.5 0 01-8.337 2.083m-7.5-2.963A3.426 3.426 0 003 6.57a4.5 4.5 0 018.337 2.083v.357a4.5 4.5 0 01-8.337 2.083zM3 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m5.963 2.72a3.426 3.426 0 01-3.163 2.083 4.5 4.5 0 01-8.337-2.083v-.357a4.5 4.5 0 018.337-2.083m5.963 2.72a3.426 3.426 0 013.163 2.083 4.5 4.5 0 01-8.337-2.083v-.357a4.5 4.5 0 018.337-2.083z" />
            </svg>
            <span class="font-medium">Pengguna</span>
        </a>
        
        {{-- Link History Peminjaman --}}
        <a href="{{ route('admin.history.index') }}" class="{{ request()->routeIs('admin.history.index') ? 'bg-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-100' }} flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
            <span class="font-medium">History Peminjaman</span>
        </a>
    </nav>
</aside>