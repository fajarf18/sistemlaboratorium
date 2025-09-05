<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class ForgotPasswordController extends Controller
{
    // TAHAP 1: Menampilkan form untuk memasukkan email
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password.enter-email');
    }

    // TAHAP 2: Validasi email, buat & kirim OTP
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan dalam sistem kami.']);
        }

        // Buat OTP dan waktu kedaluwarsa
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = now()->addSeconds(60); // OTP berlaku 60 detik
        $user->save();

        // Kirim OTP ke email
        Mail::to($user->email)->send(new OtpMail($otp));

        // Simpan email di session untuk tahap selanjutnya
        $request->session()->put('email_for_password_reset', $user->email);

        return redirect()->route('password.otp.form')->with('status', 'Kode OTP telah dikirim ke email Anda.');
    }
    
    // TAHAP 3: Menampilkan form untuk verifikasi OTP
    public function showOtpForm()
    {
        if (!session('email_for_password_reset')) {
            return redirect()->route('password.request')->withErrors(['email' => 'Sesi tidak valid, silakan ulangi.']);
        }
        return view('auth.forgot-password.verify-otp');
    }

    // TAHAP 4: Memvalidasi OTP
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|numeric|digits:6']);

        $email = $request->session()->get('email_for_password_reset');
        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Sesi Anda telah berakhir.']);
        }

        $user = User::where('email', $email)
                    ->where('otp', $request->otp)
                    ->first();

        if (!$user || $user->otp_expires_at < now()) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau telah kedaluwarsa.']);
        }

        // Simpan penanda di session bahwa OTP sudah terverifikasi
        $request->session()->put('otp_verified', true);

        return redirect()->route('password.reset.form');
    }

    // TAHAP 5: Menampilkan form untuk reset password baru
    public function showResetForm(Request $request)
    {
        if (!$request->session()->get('otp_verified')) {
            return redirect()->route('password.request')->withErrors(['email' => 'Anda harus verifikasi OTP terlebih dahulu.']);
        }
        return view('auth.forgot-password.reset-password');
    }

    // TAHAP 6: Mengubah password di database
    public function resetPassword(Request $request)
    {
        if (!$request->session()->get('otp_verified') || !$request->session()->get('email_for_password_reset')) {
            return redirect()->route('password.request')->withErrors(['email' => 'Sesi tidak valid.']);
        }

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        
        $email = $request->session()->get('email_for_password_reset');
        $user = User::where('email', $email)->first();

        if (!$user) {
             return redirect()->route('password.request')->withErrors(['email' => 'Terjadi kesalahan, pengguna tidak ditemukan.']);
        }

        // Update password & hapus OTP
        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        // Hapus session
        $request->session()->forget(['email_for_password_reset', 'otp_verified']);

        // Login kan user
        Auth::login($user);

        return redirect('/dashboard')->with('status', 'Password Anda telah berhasil diubah!');
    }
}