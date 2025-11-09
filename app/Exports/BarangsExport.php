<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BarangsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Load relasi units hanya yang rusak (kini rusak_ringan & rusak_berat) untuk efisiensi
        return Barang::with(['units' => function($query) {
            $query->whereIn('status', ['rusak_ringan', 'rusak_berat'])->select('id', 'barang_id', 'unit_code', 'status');
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
            'Jumlah Rusak Ringan',
            'ID Unit Rusak Ringan',
            'Jumlah Rusak Berat',
            'ID Unit Rusak Berat',
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

        $unitRusakRingan = $barang->units->where('status', 'rusak_ringan');
        $unitRusakBerat = $barang->units->where('status', 'rusak_berat');

        return [
            $no,
            $barang->kode_barang,
            $barang->nama_barang,
            $barang->tipe,
            $barang->stok,
            $unitRusakRingan->count(),
            $unitRusakRingan->pluck('unit_code')->implode(', ') ?: '-',
            $unitRusakBerat->count(),
            $unitRusakBerat->pluck('unit_code')->implode(', ') ?: '-',
            $barang->deskripsi ?? '-',
            $barang->created_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * Styling untuk Excel (hanya header)
     */
    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Title
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:K1');
                $sheet->setCellValue('A1', 'Data Barang Laboratorium');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                $headerRow = 3;
                $sheet->getStyle("A{$headerRow}:K{$headerRow}")
                    ->getFont()->setBold(true);
                $sheet->getStyle("A{$headerRow}:K{$headerRow}")
                    ->getFont()->getColor()->setRGB('FFFFFF');
                $sheet->getStyle("A{$headerRow}:K{$headerRow}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1F4E78');
                $sheet->getStyle("A{$headerRow}:K{$headerRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A{$headerRow}:K{$highestRow}")
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '4B5563'],
                            ],
                            'outline' => [
                                'borderStyle' => Border::BORDER_MEDIUM,
                                'color' => ['rgb' => '1F2937'],
                            ],
                        ],
                    ]);

                for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:K{$row}")
                            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F7F9FC');
                    }
                }

                // Signature block
                $startRow = $highestRow + 3;
                $columns = ['C', 'G', 'J'];

                foreach ($this->signatureEntries() as $index => $signature) {
                    $column = $columns[$index] ?? end($columns);
                    $cellRange = $column . $startRow . ':' . $column . ($startRow + 4);

                    $sheet->setCellValue($column . $startRow, $signature['label']);
                    $sheet->setCellValue($column . ($startRow + 3), $signature['name']);
                    $sheet->setCellValue($column . ($startRow + 4), $signature['nid']);

                    $sheet->getStyle($cellRange)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle($cellRange)->applyFromArray([
                        'borders' => [
                            'outline' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '9CA3AF'],
                            ],
                            'vertical' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'E5E7EB'],
                            ],
                        ],
                    ]);
                }
            },
        ];
    }

    protected function signatureEntries(): array
    {
        return [
            [
                'label' => 'Laboran',
                'name' => $this->signatureName('laboran'),
                'nid' => $this->signatureNid('laboran'),
            ],
            [
                'label' => 'Ketua Lab',
                'name' => $this->signatureName('ketua_lab'),
                'nid' => $this->signatureNid('ketua_lab'),
            ],
            [
                'label' => 'Ketua Jurusan',
                'name' => $this->signatureName('ketua_jurusan'),
                'nid' => $this->signatureNid('ketua_jurusan'),
            ],
        ];
    }

    protected function signatureName(string $key): string
    {
        return config("app.signatures.{$key}.name") ?? '__________________________';
    }

    protected function signatureNid(string $key): string
    {
        $nid = config("app.signatures.{$key}.nid");

        return 'NID: ' . ($nid ?? '__________________________');
    }
}
