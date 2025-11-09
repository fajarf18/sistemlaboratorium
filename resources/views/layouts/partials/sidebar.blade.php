{{-- Overlay untuk background gelap saat sidebar terbuka di mobile --}}
<div x-show="sidebarOpen" class="fixed inset-0 z-20 bg-black bg-opacity-50 transition-opacity lg:hidden" @click="sidebarOpen = false"></div>

<aside 
    class="fixed inset-y-0 left-0 z-30 flex w-64 flex-col bg-sky-50 p-4 transition-transform duration-300"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3 px-4 py-2">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-sky-400 text-white">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" /></svg>
            </div>
            <span class="text-xl font-bold text-gray-800">Sistem Inventory</span>
        </div>
        <button x-on:click="sidebarOpen = false" class="text-gray-500 focus:outline-none lg:hidden">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    @php
        $hasActiveBorrowing = \App\Models\Peminjaman::where('user_id', auth()->id())
            ->where('status', 'Dipinjam')
            ->exists();
    @endphp

    <nav class="mt-8 flex flex-1 flex-col space-y-1">
        {{-- Link Dashboard --}}
        <a href="{{ route('user.dashboard') }}" class="{{ request()->routeIs('user.dashboard') ? 'bg-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-100' }} flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
            <span class="font-medium">Dashboard</span>
        </a>

        {{-- Link Pinjam Barang --}}
        @if($hasActiveBorrowing)
            <div class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-gray-400 cursor-not-allowed opacity-60" title="Tidak dapat diakses karena masih ada peminjaman aktif">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15m0-3l-3-3m0 0l-3 3m3-3v11.25" /></svg>
                <span class="font-medium">Pinjam Barang</span>
                <svg class="h-4 w-4 ml-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
            </div>
        @else
            <a href="{{ route('user.pinjam.index') }}" class="{{ request()->routeIs('user.pinjam.index') ? 'bg-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-100' }} flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15m0-3l-3-3m0 0l-3 3m3-3v11.25" /></svg>
                <span class="font-medium">Pinjam Barang</span>
            </a>
        @endif

        {{-- Link Modul Praktikum --}}
        @if($hasActiveBorrowing)
            <div class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-gray-400 cursor-not-allowed opacity-60" title="Tidak dapat diakses karena masih ada peminjaman aktif">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
                </svg>
                <span class="font-medium">Modul Praktikum</span>
                <svg class="h-4 w-4 ml-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
            </div>
        @else
            <a href="{{ route('user.modul.index') }}" class="{{ request()->routeIs('user.modul.*') ? 'bg-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-100' }} flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
                </svg>
                <span class="font-medium">Modul Praktikum</span>
            </a>
        @endif

        {{-- Link Kembalikan Barang --}}
        <a href="{{ route('user.kembalikan.index') }}" class="{{ request()->routeIs('user.kembalikan.index') ? 'bg-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-100' }} flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg>
            <span class="font-medium">Kembalikan Barang</span>
        </a>

        {{-- Link Keranjang --}}
        @if($hasActiveBorrowing)
            <div class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-gray-400 cursor-not-allowed opacity-60" title="Tidak dapat diakses karena masih ada peminjaman aktif">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c.51 0 .962-.344 1.084-.845l1.956-7.146A.75.75 0 0020.25 6h-15.75" /></svg>
                <span class="font-medium">Keranjang</span>
                <svg class="h-4 w-4 ml-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
            </div>
        @else
            <a href="{{ route('user.keranjang.index') }}" class="{{ request()->routeIs('user.keranjang.index') ? 'bg-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-100' }} flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c.51 0 .962-.344 1.084-.845l1.956-7.146A.75.75 0 0020.25 6h-15.75" /></svg>
                <span class="font-medium">Keranjang</span>
            </a>
        @endif

        {{-- Link Rincian Pinjaman --}}
        <a href="{{ route('user.peminjaman.rincian') }}" class="{{ request()->routeIs('user.peminjaman.rincian') ? 'bg-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-100' }} flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
            <span class="font-medium">Rincian Pinjaman</span>
        </a>

        {{-- Link History Peminjaman --}}
        <a href="{{ route('user.history.index') }}" class="{{ request()->routeIs('user.history.index') ? 'bg-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-100' }} flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="font-medium">History Peminjaman</span>
        </a>

        {{-- Link Profil Pengguna --}}
        <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'bg-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-100' }} flex items-center gap-3 rounded-lg px-4 py-2.5 transition-all">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            <span class="font-medium">Profil Pengguna</span>
        </a>
    </nav>
    
</aside>