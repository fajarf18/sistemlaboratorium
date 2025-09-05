<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Reset Password</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans">
    <div class="min-h-screen flex flex-col justify-center items-center px-4 bg-gradient-to-br from-[#30BEB6] to-[#3069BE]">
        <div class="w-full max-w-md bg-white p-8 sm:p-10 border rounded-2xl shadow-lg">
            <div class="flex flex-col items-center mb-6">
                <h1 class="text-2xl font-bold text-slate-800">Buat Password Baru</h1>
                <p class="text-sm text-slate-500 mt-1">Verifikasi berhasil. Silakan atur password baru Anda.</p>
            </div>

            <form method="POST" action="{{ route('password.update.new') }}" class="space-y-4">
                @csrf
                <div>
                    <x-input-label for="password" value="Password Baru" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="password_confirmation" value="Konfirmasi Password Baru" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
                <div class="pt-2">
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 border rounded-md font-semibold text-base text-white uppercase bg-gradient-to-r from-[#30BEB6] to-[#3069BE] hover:shadow-lg">
                        Simpan Password Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>