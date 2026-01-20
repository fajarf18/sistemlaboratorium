<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasPraktikum extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kelas',
        'mata_kuliah',
        'deskripsi',
        'created_by', // User ID (Dosen)
        'modul_id',
        'is_active',
        'tanggal_praktikum',
        'jam_mulai',
        'jam_selesai',
    ];

    public function modul()
    {
        return $this->belongsTo(Modul::class);
    }

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_praktikum' => 'date',
    ];

    /**
     * Relasi ke User yang membuat kelas (dosen)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Alias untuk creator (dosen pengampu)
     */
    public function dosenPengampu()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke items (barang-barang dalam kelas)
     */
    public function items()
    {
        return $this->hasMany(KelasPraktikumItem::class);
    }

    /**
     * Relasi ke mahasiswa yang join kelas
     */
    public function mahasiswa()
    {
        return $this->belongsToMany(User::class, 'kelas_praktikum_user', 'kelas_praktikum_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Relasi ke keranjang
     */
    public function keranjangs()
    {
        return $this->hasMany(Keranjang::class);
    }
}
