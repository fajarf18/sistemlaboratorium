<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
 // app/Models/User.php
protected $fillable = [
    'nama',
    'nim',
    'email',
    'nomor_wa',
    'prodi',
    'semester',
    'password',
    'role',
    'otp', // <-- TAMBAHKAN INI
    'otp_expires_at', // Tambahkan role
    'email_verified_at',
];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function keranjangs()
    {
        return $this->hasMany(Keranjang::class);
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }



    /**
     * Relasi ke kelas praktikum yang dibuat dosen
     */
    public function kelasPraktikumsCreated()
    {
        return $this->hasMany(KelasPraktikum::class, 'created_by');
    }

    /**
     * Relasi ke kelas praktikum yang diikuti mahasiswa
     */
    public function kelasPraktikumsJoined()
    {
        return $this->belongsToMany(KelasPraktikum::class, 'kelas_praktikum_user', 'user_id', 'kelas_praktikum_id')
                    ->withTimestamps();
    }
    public function moduls()
    {
        return $this->hasMany(Modul::class, 'user_id');
    }

    public function peminjamansAsDosen()
    {
        return $this->hasMany(Peminjaman::class, 'dosen_id');
    }
    
    public function keranjangsAsDosen()
    {
        return $this->hasMany(Keranjang::class, 'dosen_id');
    }
}
