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

    public function detailPeminjaman()
    {
        return $this->hasMany(DetailPeminjaman::class);
    }

        public function detailPeminjamans()
    {
        return $this->hasMany(DetailPeminjaman::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendefinisikan relasi "satu ke satu".
     * Satu peminjaman akan memiliki satu histori pengembalian.
     */
    public function history()
    {
        return $this->hasOne(HistoryPeminjaman::class);
    }
}

