<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail; // Pastikan ini ada
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Mail\OtpMail; // Pastikan ini ada

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'prodi' => ['required', 'string', 'max:255'],
            'semester' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Buat OTP 6 digit
        $otp = rand(100000, 999999);

        // --- BAGIAN KRITIS YANG DIPERBAIKI ---
        $user = User::create([
            'nama' => $request->nama,
            'nim' => $request->nim,
            'email' => $request->email,
            'prodi' => $request->prodi,
            'semester' => $request->semester,
            'password' => Hash::make($request->password),
            'otp' => $otp, // <-- INI YANG TERLEWAT
            'otp_expires_at' => now()->addSeconds(60), // <-- DAN INI
        ]);
        // --- AKHIR BAGIAN PERBAIKAN ---

        // Kirim email yang berisi kode OTP
        Mail::to($user->email)->send(new OtpMail($otp));

        // Simpan email di session untuk halaman verifikasi
        $request->session()->put('email_for_otp_verification', $user->email);

        // Arahkan ke halaman verifikasi OTP
        return redirect()->route('otp.form')->with('status', 'Kode OTP telah dikirim ke email Anda. Silakan cek kotak masuk.');
    }
}