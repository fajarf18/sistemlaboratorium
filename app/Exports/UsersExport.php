<?php

namespace App\Exports;

use App\Models\User;
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

class UsersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $search;
    protected $prodi;
    protected $semester;

    public function __construct($search = null, $prodi = null, $semester = null)
    {
        $this->search = $search;
        $this->prodi = $prodi;
        $this->semester = $semester;
    }

    public function query()
    {
        $query = User::query()->where('role', 'user');

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('nim', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('nomor_wa', 'like', '%' . $search . '%');
            });
        }

        if ($this->prodi) {
            $query->where('prodi', $this->prodi);
        }

        if ($this->semester) {
            $query->where('semester', $this->semester);
        }

        return $query->orderBy('nama');
    }

    public function headings(): array
    {
        return [
            'No',
            'NIM/NIP',
            'Nama',
            'Email',
            'Nomor WA',
            'Prodi',
            'Semester',
            'Tanggal Daftar',
        ];
    }

    public function map($user): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $user->nim ?? '-',
            $user->nama ?? '-',
            $user->email ?? '-',
            $user->nomor_wa ?? '-',
            $user->prodi ?? '-',
            $user->semester ?? '-',
            $user->created_at ? Carbon::parse($user->created_at)->format('d/m/Y') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Sisipkan judul
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', $this->buildTitle());
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // Header
                $headerRow = 3;
                $sheet->getStyle("A{$headerRow}:H{$headerRow}")
                    ->getFont()->setBold(true);
                $sheet->getStyle("A{$headerRow}:H{$headerRow}")
                    ->getFont()->getColor()->setRGB('FFFFFF');
                $sheet->getStyle("A{$headerRow}:H{$headerRow}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1F4E78');
                $sheet->getStyle("A{$headerRow}:H{$headerRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                // Border tabel
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A{$headerRow}:H{$highestRow}")
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

                // Striping
                for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:H{$row}")
                            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F7F9FC');
                    }
                }
            },
        ];
    }

    protected function buildTitle(): string
    {
        $base = 'Data Pengguna';
        $filters = [];
        if ($this->prodi) $filters[] = "Prodi: {$this->prodi}";
        if ($this->semester) $filters[] = "Semester: {$this->semester}";
        if (!empty($filters)) {
            return $base . ' (' . implode(', ', $filters) . ')';
        }
        return $base;
    }
}
