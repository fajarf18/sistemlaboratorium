<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModulItem extends Model
{
    protected $fillable = [
        'kode_modul',
        'kode_barang',
        'jumlah'
    ];

    public function modul()
    {
        return $this->belongsTo(Modul::class, 'kode_modul', 'kode_modul');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }
}
