<?php

namespace App\Exports;

use App\Models\Peminjaman;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HistoryPeminjamanExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    /**
     * Query data peminjaman yang sudah dikembalikan
     */
    public function query()
    {
        $query = Peminjaman::with([
            'user',
            'dosenPengampu',
            'detailPeminjaman.barang',
            'detailPeminjaman.peminjamanUnits.barangUnit',
            'history'
        ])->where('status', 'Dikembalikan');

        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('nim', 'like', '%' . $this->search . '%');
            });
        }

        return $query->orderBy('tanggal_kembali', 'desc');
    }

    /**
     * Heading kolom Excel
     */
    public function headings(): array
    {
        return [
            'No',
            'NIM',
            'Nama Peminjam',
            'Email',
            'Nomor WA',
            'Dosen Pengampu',
            'Mata Kuliah',
            'Tanggal Pinjam',
            'Tanggal Wajib Kembali',
            'Tanggal Kembali',
            'Barang yang Dipinjam',
            'Total Unit',
            'Status Pengembalian',
            'Unit Rusak',
            'Unit Hilang',
            'Keterangan',
        ];
    }

    /**
     * Mapping data untuk setiap row
     */
    public function map($peminjaman): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        // Ambil semua barang yang dipinjam
        $barangList = [];
        $totalUnits = 0;
        $unitRusak = [];
        $unitHilang = [];

        foreach ($peminjaman->detailPeminjaman as $detail) {
            $jumlahUnit = $detail->peminjamanUnits->count();
            $totalUnits += $jumlahUnit;
            $barangList[] = $detail->barang->nama_barang . ' (' . $jumlahUnit . ' unit)';

            // Kumpulkan unit yang rusak atau hilang
            foreach ($detail->peminjamanUnits as $peminjamanUnit) {
                if ($peminjamanUnit->status_pengembalian === 'rusak' && $peminjamanUnit->barangUnit) {
                    $unitRusak[] = $peminjamanUnit->barangUnit->unit_code . ' (' . $detail->barang->nama_barang . ')';
                }
                if ($peminjamanUnit->status_pengembalian === 'hilang' && $peminjamanUnit->barangUnit) {
                    $unitHilang[] = $peminjamanUnit->barangUnit->unit_code . ' (' . $detail->barang->nama_barang . ')';
                }
            }
        }

        return [
            $rowNumber,
            $peminjaman->user->nim ?? '-',
            $peminjaman->user->nama ?? '-',
            $peminjaman->user->email ?? '-',
            $peminjaman->user->nomor_wa ?? '-',
            $peminjaman->dosenPengampu->nama ?? '-',
            $peminjaman->dosenPengampu->mata_kuliah ?? '-',
            \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y'),
            \Carbon\Carbon::parse($peminjaman->tanggal_wajib_kembali)->format('d/m/Y'),
            \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d/m/Y'),
            implode(', ', $barangList),
            $totalUnits,
            $peminjaman->history->status_pengembalian ?? '-',
            !empty($unitRusak) ? implode(', ', $unitRusak) : '-',
            !empty($unitHilang) ? implode(', ', $unitHilang) : '-',
            $peminjaman->history->deskripsi_kehilangan ?? '-',
        ];
    }

    /**
     * Styling untuk Excel
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ];
    }
}
