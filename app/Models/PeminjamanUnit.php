<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'detail_peminjaman_id',
        'barang_unit_id',
        'status_pengembalian',
        'keterangan_kondisi',
        'foto_kondisi',
    ];

    /**
     * Relasi ke detail peminjaman
     */
    public function detailPeminjaman()
    {
        return $this->belongsTo(DetailPeminjaman::class);
    }

    /**
     * Relasi ke barang unit
     */
    public function barangUnit()
    {
        return $this->belongsTo(BarangUnit::class);
    }
}
