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

    protected $appends = [
        'total_stok',
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
        // Rusak sekarang terbagi menjadi dua tipe: rusak_ringan dan rusak_berat
        return $this->hasMany(BarangUnit::class)->whereIn('status', ['rusak_ringan', 'rusak_berat']);
    }

    /**
     * Get units rusak ringan
     */
    public function unitsRusakRingan()
    {
        return $this->hasMany(BarangUnit::class)->where('status', 'rusak_ringan');
    }

    /**
     * Get units rusak berat
     */
    public function unitsRusakBerat()
    {
        return $this->hasMany(BarangUnit::class)->where('status', 'rusak_berat');
    }

    /**
     * Get units dengan status hilang
     */
    public function unitsMissing()
    {
        // Status 'hilang' sudah dihapus dari sistem. Kembalikan relasi kosong untuk kompatibilitas.
        return $this->hasMany(BarangUnit::class)->whereRaw("0=1");
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
        return $this->units()->whereIn('status', ['rusak_ringan', 'rusak_berat'])->count();
    }

    /**
     * Accessor untuk jumlah stok rusak ringan
     */
    public function getStokRusakRinganAttribute()
    {
        return $this->units()->where('status', 'rusak_ringan')->count();
    }

    /**
     * Accessor untuk jumlah stok rusak berat
     */
    public function getStokRusakBeratAttribute()
    {
        return $this->units()->where('status', 'rusak_berat')->count();
    }

    /**
     * Accessor untuk jumlah stok hilang
     */
    public function getStokHilangAttribute()
    {
        // Status hilang tidak lagi digunakan
        return 0;
    }

    /**
     * Accessor untuk total stok
     */
    public function getTotalStokAttribute()
    {
        if (array_key_exists('units_count', $this->attributes)) {
            return $this->attributes['units_count'];
        }

        return $this->units()->count();
    }
}
