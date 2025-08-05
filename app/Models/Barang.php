<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_barang',
        'tipe',
        'stok',
        'gambar',
    ];
    public function keranjangs()
{
    return $this->hasMany(Keranjang::class);
}
}