<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_barang',
        'kode_barang',
        'tipe',
        'stok',
        'gambar',
        'deskripsi',
    ];

    /**
     * Relasi ke tabel barang_units
     */
    public function units()
    {
        return $this->hasMany(BarangUnit::class);
    }

    /**
     * Get units dengan status baik
     */
    public function unitsGood()
    {
        return $this->hasMany(BarangUnit::class)->where('status', 'baik');
    }

    /**
     * Get units dengan status rusak
     */
    public function unitsDamaged()
    {
        return $this->hasMany(BarangUnit::class)->where('status', 'rusak');
    }

    /**
     * Get units dengan status hilang
     */
    public function unitsMissing()
    {
        return $this->hasMany(BarangUnit::class)->where('status', 'hilang');
    }

    /**
     * Get units dengan status dipinjam
     */
    public function unitsBorrowed()
    {
        return $this->hasMany(BarangUnit::class)->where('status', 'dipinjam');
    }

    /**
     * Accessor untuk jumlah stok baik
     */
    public function getStokBaikAttribute()
    {
        return $this->units()->where('status', 'baik')->count();
    }

    /**
     * Accessor untuk jumlah stok dipinjam
     */
    public function getStokDipinjamAttribute()
    {
        return $this->units()->where('status', 'dipinjam')->count();
    }

    /**
     * Accessor untuk jumlah stok rusak
     */
    public function getStokRusakAttribute()
    {
        return $this->units()->where('status', 'rusak')->count();
    }

    /**
     * Accessor untuk jumlah stok hilang
     */
    public function getStokHilangAttribute()
    {
        return $this->units()->where('status', 'hilang')->count();
    }

    /**
     * Accessor untuk total stok
     */
    public function getTotalStokAttribute()
    {
        return $this->units()->count();
    }
}
