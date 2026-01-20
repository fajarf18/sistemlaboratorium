<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasPraktikumItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'kelas_praktikum_id',
        'barang_id',
        'jumlah_default',
        'keterangan',
    ];

    /**
     * Relasi ke KelasPraktikum
     */
    public function kelasPraktikum()
    {
        return $this->belongsTo(KelasPraktikum::class);
    }

    /**
     * Relasi ke Barang
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
