<?php

namespace App\Exports;

use App\Models\Peminjaman;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
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

class HistoryPeminjamanExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $search;
    protected $startDate;
    protected $endDate;

    public function __construct($search = null, $startDate = null, $endDate = null)
    {
        $this->search = $search;
        [$this->startDate, $this->endDate] = $this->normalizeDateRange($startDate, $endDate);
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

        if ($this->startDate) {
            $query->whereDate('tanggal_kembali', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('tanggal_kembali', '<=', $this->endDate);
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
            //'Unit Hilang', (status hilang dihapus)
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

            // Kumpulkan unit yang rusak (kedua tipe)
            foreach ($detail->peminjamanUnits as $peminjamanUnit) {
                if ($peminjamanUnit->barangUnit && in_array($peminjamanUnit->status_pengembalian, ['rusak_ringan', 'rusak_berat'])) {
                    $unitRusak[] = $peminjamanUnit->barangUnit->unit_code . ' (' . $detail->barang->nama_barang . ')';
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
            Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y'),
            Carbon::parse($peminjaman->tanggal_wajib_kembali)->format('d/m/Y'),
            Carbon::parse($peminjaman->tanggal_kembali)->format('d/m/Y'),
            implode(', ', $barangList),
            $totalUnits,
            $peminjaman->final_status_pengembalian ?? '-',
            !empty($unitRusak) ? implode(', ', $unitRusak) : '-',
            // Hilang dihapus
            $peminjaman->history->deskripsi_kehilangan ?? '-',
        ];
    }

    /**
     * Styling untuk Excel
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

                // Sisipkan ruang untuk judul
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:O1');
                $sheet->setCellValue('A1', $this->buildTitle());
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // Styling header tabel
                $headerRow = 3;
                $sheet->getStyle("A{$headerRow}:O{$headerRow}")
                    ->getFont()->setBold(true);
                $sheet->getStyle("A{$headerRow}:O{$headerRow}")
                    ->getFont()->getColor()->setRGB('FFFFFF');
                $sheet->getStyle("A{$headerRow}:O{$headerRow}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1F4E78');
                $sheet->getStyle("A{$headerRow}:O{$headerRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                // Border untuk seluruh tabel
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A{$headerRow}:O{$highestRow}")
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

                // Stripe shading untuk readability
                for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:O{$row}")
                            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F7F9FC');
                    }
                }

                // Area tanda tangan
                $startRow = $highestRow + 3;
                $columns = ['B', 'G', 'L'];

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

    protected function normalizeDateRange(?string $startDate, ?string $endDate): array
    {
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            if ($end->lessThan($start)) {
                return [$end->toDateString(), $start->toDateString()];
            }
        }

        return [$startDate, $endDate];
    }

    protected function buildTitle(): string
    {
        $start = $this->formatDateLabel($this->startDate);
        $end = $this->formatDateLabel($this->endDate);

        if ($start && $end) {
            return "Peminjaman data barang pada tanggal {$start} sampai {$end}";
        }

        if ($start) {
            return "Peminjaman data barang sejak {$start}";
        }

        if ($end) {
            return "Peminjaman data barang hingga {$end}";
        }

        return 'Peminjaman data barang - seluruh periode';
    }

    protected function formatDateLabel(?string $date): ?string
    {
        return $date ? Carbon::parse($date)->translatedFormat('d M Y') : null;
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
