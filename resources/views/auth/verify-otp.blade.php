<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- (Bagian head tetap sama) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans">
    <div class="min-h-screen flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-[#30BEB6] to-[#3069BE]">
        
        <div class="w-full max-w-md bg-white p-8 sm:p-10 border border-slate-200 rounded-2xl shadow-lg">
            
            <div class="flex flex-col items-center mb-6">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Logo STIKes" class="w-20 h-20 mb-4">
                <h1 class="text-2xl font-bold text-slate-800">Verifikasi Akun Anda</h1>
                <p class="text-sm text-slate-500 mt-1 text-center">
                    Kami telah mengirimkan kode OTP ke email Anda. Silakan masukkan kode di bawah ini.
                </p>
            </div>

            {{-- Menampilkan pesan status jika ada --}}
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('otp.verify') }}" class="space-y-4">
                @csrf

                <div class="relative">
                    <x-text-input id="otp" type="text" name="otp" :value="old('otp')" required placeholder="Masukkan Kode OTP" class="pl-10 w-full bg-slate-50"/>
                    <x-input-error :messages="$errors->get('otp')" class="mt-2" />
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent rounded-md font-semibold text-base text-white uppercase tracking-widest transition ease-in-out duration-150 bg-gradient-to-r from-[#30BEB6] to-[#3069BE] hover:shadow-lg hover:shadow-cyan-500/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                        {{ __('Verifikasi') }}
                    </button>
                </div>
            </form>

            <div class="text-center mt-4 text-sm text-slate-500">
    {{-- Tampilkan sisa waktu awal dari controller --}}
    <p id="timer-text">Tidak menerima kode? Kirim ulang dalam <span id="timer" class="font-semibold text-slate-800">{{ $timeLeft }}</span> detik.</p>
    <form id="resend-form" method="POST" action="{{ route('otp.resend') }}" class="hidden">
        @csrf
        <button type="submit" class="underline hover:text-cyan-600">Kirim Ulang OTP</button>
    </form>
</div>
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let timerElement = document.getElementById('timer');
        let timerText = document.getElementById('timer-text');
        let resendForm = document.getElementById('resend-form');
        
        // --- BAGIAN YANG DIPERBAIKI ---
        // Ambil nilai awal dari PHP, bukan di-set manual ke 60
        let timeLeft = {{ $timeLeft }};
        // --- AKHIR PERBAIKAN ---

        // Sembunyikan timer dan tampilkan tombol resend jika waktu sudah habis saat halaman dimuat
        if (timeLeft <= 0) {
            timerText.classList.add('hidden');
            resendForm.classList.remove('hidden');
        }

        const countdown = setInterval(() => {
            if (timeLeft <= 0) {
                clearInterval(countdown);
                timerText.classList.add('hidden');
                resendForm.classList.remove('hidden');
            } else {
                // Pastikan timer text terlihat jika masih ada waktu
                timerText.classList.remove('hidden');
                resendForm.classList.add('hidden');

                timerElement.textContent = timeLeft;
                timeLeft--;
            }
        }, 1000);
    });
</script>
</body>
</html>