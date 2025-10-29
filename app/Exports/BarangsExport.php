<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class BarangsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Load relasi units hanya yang rusak dan hilang untuk efisiensi
        return Barang::with(['units' => function($query) {
            $query->whereIn('status', ['rusak', 'hilang'])->select('id', 'barang_id', 'unit_code', 'status');
        }])->select('id', 'kode_barang', 'nama_barang', 'tipe', 'stok', 'deskripsi', 'created_at')->get();
    }

    /**
     * Headings untuk Excel
     */
    public function headings(): array
    {
        return [
            'No',
            'Kode Barang',
            'Nama Barang',
            'Tipe',
            'Stok',
            'Jumlah Unit Rusak',
            'ID Unit Rusak',
            'Jumlah Unit Hilang',
            'ID Unit Hilang',
            'Deskripsi',
            'Tanggal Dibuat',
        ];
    }

    /**
     * Mapping data untuk setiap row
     */
    public function map($barang): array
    {
        static $no = 0;
        $no++;

        // Ambil unit yang rusak
        $unitRusak = $barang->units->where('status', 'rusak');
        $jumlahRusak = $unitRusak->count();
        $idUnitRusak = $unitRusak->pluck('unit_code')->implode(', ');

        // Ambil unit yang hilang
        $unitHilang = $barang->units->where('status', 'hilang');
        $jumlahHilang = $unitHilang->count();
        $idUnitHilang = $unitHilang->pluck('unit_code')->implode(', ');

        return [
            $no,
            $barang->kode_barang,
            $barang->nama_barang,
            $barang->tipe,
            $barang->stok,
            $jumlahRusak,
            $idUnitRusak ?: '-',
            $jumlahHilang,
            $idUnitHilang ?: '-',
            $barang->deskripsi ?? '-',
            $barang->created_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * Styling untuk Excel (hanya header)
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header saja
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}
