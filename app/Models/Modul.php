<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modul extends Model
{
    protected $fillable = [
        'nama_modul',
        'deskripsi',
        'kode_modul',
        'is_active',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function items()
    {
        return $this->hasMany(ModulItem::class, 'kode_modul', 'kode_modul');
    }
}
