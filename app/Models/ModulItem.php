<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModulItem extends Model
{
    protected $fillable = [
        'modul_id',
        'barang_id',
        'jumlah'
    ];

    public function modul()
    {
        return $this->belongsTo(Modul::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
