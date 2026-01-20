<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    use HasFactory;
    protected $table = 'keranjangs';
    protected $fillable = [
        'user_id', 
        'barang_id', 
        'jumlah',
        'kelas_praktikum_id',
        'kelas_praktikum_id',
        'dosen_id',
        'dari_kelas'
    ];

    protected $casts = [
        'dari_kelas' => 'boolean',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke KelasPraktikum
     */
    public function kelasPraktikum()
    {
        return $this->belongsTo(KelasPraktikum::class);
    }

    /**
     * Relasi ke DosenPengampu
     */
    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }
}