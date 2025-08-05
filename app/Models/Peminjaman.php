<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';

    protected $fillable = [
        'user_id',
        'tanggal_pinjam',
        'tanggal_wajib_kembali',
        'tanggal_kembali',
        'status',
    ];

    /**
     * Mendefinisikan relasi "satu ke banyak".
     * Satu peminjaman bisa memiliki banyak detail peminjaman.
     */
    public function detailPeminjamans()
    {
        return $this->hasMany(DetailPeminjaman::class);
    }
}