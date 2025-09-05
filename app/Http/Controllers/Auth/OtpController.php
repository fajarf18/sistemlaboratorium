<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class OtpController extends Controller
{
    /**
     * Menampilkan form untuk memasukkan OTP.
     */
    public function showOtpForm()
{
    $email = session('email_for_otp_verification');

    if (!$email) {
        return redirect()->route('register')->withErrors(['email' => 'Sesi verifikasi tidak ditemukan. Silakan daftar kembali.']);
    }
    
    $user = User::where('email', $email)->first();

    // Jika user tidak ditemukan atau tidak punya waktu kedaluwarsa OTP
    if (!$user || !$user->otp_expires_at) {
        return redirect()->route('register')->withErrors(['email' => 'Terjadi kesalahan. Silakan daftar kembali.']);
    }

    // --- BAGIAN BARU: HITUNG SISA WAKTU ---
    // Pastikan 'otp_expires_at' adalah objek Carbon untuk perbandingan
    $expiresAt = \Carbon\Carbon::parse($user->otp_expires_at);
    
    // Hitung selisih waktu dalam detik. Jika sudah lewat, hasilnya 0.
    $timeLeft = (int) (now()->lt($expiresAt) ? now()->diffInSeconds($expiresAt) : 0);
    // --- AKHIR BAGIAN BARU ---

    return view('auth.verify-otp', ['timeLeft' => $timeLeft]);
}

    /**
     * Memverifikasi OTP yang dimasukkan pengguna.
     */
public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $email = $request->session()->get('email_for_otp_verification');

        if (!$email) {
            return back()->withErrors(['otp' => 'Sesi Anda telah berakhir. Silakan coba mendaftar ulang.']);
        }

        $user = User::where('email', $email)
                    ->where('otp', $request->otp)
                    ->first();

        if (!$user) {
            return back()->withErrors(['otp' => 'Kode OTP yang Anda masukkan salah.']);
        }

        if ($user->otp_expires_at < now()) {
            return back()->withErrors(['otp' => 'Kode OTP telah kedaluwarsa. Silakan minta kode baru.']);
        }

        // --- INI BAGIAN PENTINGNYA ---
        // Jika semua valid, update data user
        
        // 1. Mengisi kolom 'email_verified_at' dengan waktu saat ini.
        $user->email_verified_at = now(); 
        
        // 2. Mengosongkan OTP setelah berhasil.
        $user->otp = null; 
        $user->otp_expires_at = null;
        
        // 3. Menyimpan perubahan ke database.
        $user->save();
        // --- AKHIR BAGIAN PENTING ---

        $request->session()->forget('email_for_otp_verification');
        auth()->login($user);

        return redirect()->route('dashboard');
    }


    /**
     * Mengirim ulang OTP ke email pengguna.
     */
    public function resendOtp(Request $request)
    {
        $email = $request->session()->get('email_for_otp_verification');
        
        if (!$email) {
            return redirect()->route('register')->withErrors(['email' => 'Sesi Anda telah berakhir. Silakan coba mendaftar ulang.']);
        }

        $user = User::where('email', $email)->first();
        
        if ($user) {
            $otp = rand(100000, 999999);
            
            $user->otp = $otp;
            $user->otp_expires_at = now()->addSeconds(30); // Set waktu kedaluwarsa baru
            $user->save();
            
            Mail::to($user->email)->send(new OtpMail($otp));
            
            return redirect()->route('otp.form')->with('status', 'Kode OTP baru telah berhasil dikirim ulang ke email Anda.');
        }

        return redirect()->route('register')->withErrors(['email' => 'Terjadi kesalahan. Pengguna tidak ditemukan.']);
    }
}