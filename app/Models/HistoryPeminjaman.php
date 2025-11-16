<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryPeminjaman extends Model
{
    use HasFactory;

    /**
     * Secara eksplisit memberitahu Laravel untuk menggunakan nama tabel ini.
     */
    protected $table = 'history_peminjamans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'peminjaman_id', 
        'user_id', 
        'tanggal_kembali', 
        'status_pengembalian', 
        'deskripsi_kerusakan', 
        'gambar_bukti'
    ];
}