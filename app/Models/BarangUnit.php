<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangUnit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'barang_id',
        'unit_code',
        'status',
        'keterangan',
    ];

    /**
     * Relasi ke tabel barangs
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
