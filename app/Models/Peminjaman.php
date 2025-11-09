<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';

    protected $fillable = [
        'user_id',
        'dosen_pengampu_id',
        'tanggal_pinjam',
        'tanggal_wajib_kembali',
        'tanggal_kembali',
        'status',
    ];

    protected $appends = ['final_status_pengembalian'];

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

    public function history()
    {
        return $this->hasOne(HistoryPeminjaman::class);
    }

    public function dosenPengampu()
    {
        return $this->belongsTo(DosenPengampu::class);
    }

    public function getFinalStatusPengembalianAttribute(): string
    {
        $this->loadMissing('detailPeminjaman.peminjamanUnits');

        $hasRusakRingan = false;
        $hasRusakBerat = false;

        foreach ($this->detailPeminjaman as $detail) {
            foreach ($detail->peminjamanUnits as $unit) {
                $status = $this->normalizeUnitReturnStatus($unit->status_pengembalian);
                if ($status === 'rusak_ringan') {
                    $hasRusakRingan = true;
                } elseif ($status === 'rusak_berat') {
                    $hasRusakBerat = true;
                }
            }
        }

        $parts = [];

        if ($hasRusakRingan) {
            $parts[] = 'Rusak Ringan';
        }

        if ($hasRusakBerat) {
            $parts[] = 'Rusak Berat';
        }

        if (empty($parts)) {
            $parts[] = 'Aman';
        }

        if ($this->isPeminjamanLate()) {
            $parts[] = 'Terlambat';
        }

        return implode(' dan ', $parts);
    }

    protected function normalizeUnitReturnStatus(?string $status): ?string
    {
        if (!$status) {
            return null;
        }

        $map = [
            'rusak' => 'rusak_ringan',
            'hilang' => 'rusak_berat',
        ];

        return $map[$status] ?? $status;
    }

    protected function isPeminjamanLate(): bool
    {
        if (!$this->tanggal_kembali || !$this->tanggal_wajib_kembali) {
            return false;
        }

        return Carbon::parse($this->tanggal_kembali)
            ->greaterThan(Carbon::parse($this->tanggal_wajib_kembali));
    }
}
