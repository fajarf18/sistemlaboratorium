<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\BarangUnit;
use App\Models\DetailPeminjaman;
use App\Models\DosenPengampu;
use App\Models\HistoryPeminjaman;
use App\Models\Keranjang;
use App\Models\Peminjaman;
use App\Models\PeminjamanUnit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        Keranjang::query()->delete();
        PeminjamanUnit::query()->delete();
        DetailPeminjaman::query()->delete();
        HistoryPeminjaman::query()->delete();
        Peminjaman::query()->delete();

        $users = User::where('role', 'user')->get()->keyBy('nim');
        $dosens = DosenPengampu::all()->keyBy('nip');
        $barangMap = Barang::with(['units' => fn ($query) => $query->orderBy('unit_code')])
            ->get()
            ->keyBy('kode_barang');

        $unitPools = [];
        foreach ($barangMap as $kode => $barang) {
            $unitPools[$kode] = $barang->units->all();
        }

        $this->seedKeranjang($users, $barangMap);
        $this->seedPeminjaman($users, $dosens, $barangMap, $unitPools);
    }

    protected function seedKeranjang($users, $barangMap): void
    {
        $keranjangData = [
            ['nim' => '2211001', 'barang' => 'TR-001', 'jumlah' => 4],
            ['nim' => '2211001', 'barang' => 'PP-002', 'jumlah' => 2],
            ['nim' => '2211002', 'barang' => 'MK-001', 'jumlah' => 1],
        ];

        foreach ($keranjangData as $item) {
            $user = $users[$item['nim']] ?? null;
            $barang = $barangMap[$item['barang']] ?? null;

            if (!$user || !$barang) {
                continue;
            }

            Keranjang::create([
                'user_id' => $user->id,
                'barang_id' => $barang->id,
                'jumlah' => $item['jumlah'],
            ]);
        }
    }

    protected function seedPeminjaman($users, $dosens, $barangMap, array &$unitPools): void
    {
        $peminjamanTemplates = [
            [
                'nim' => '2211001',
                'dosen' => '19760101',
                'days_ago' => 25,
                'durasi_hari' => 3,
                'return_after_days' => 2,
                'status' => 'Dikembalikan',
                'history' => [
                    'status_pengembalian' => 'Aman',
                ],
                'details' => [
                    [
                        'barang_kode' => 'TR-001',
                        'unit_returns' => [
                            ['status' => 'dikembalikan'],
                            ['status' => 'dikembalikan'],
                        ],
                    ],
                    [
                        'barang_kode' => 'GL-001',
                        'unit_returns' => [
                            ['status' => 'dikembalikan'],
                        ],
                    ],
                ],
            ],
            [
                'nim' => '2211002',
                'dosen' => '19781212',
                'days_ago' => 18,
                'durasi_hari' => 4,
                'return_after_days' => 6,
                'status' => 'Dikembalikan',
                'history' => [
                    'status_pengembalian' => 'Rusak/Hilang dan Terlambat',
                    'deskripsi' => 'Satu pipet rusak ringan dan satu tabung pecah selama praktikum.',
                ],
                'details' => [
                    [
                        'barang_kode' => 'PP-003',
                        'unit_returns' => [
                            ['status' => 'rusak_ringan', 'note' => 'Tombol ejector macet'],
                            ['status' => 'dikembalikan'],
                        ],
                    ],
                    [
                        'barang_kode' => 'TR-002',
                        'unit_returns' => [
                            ['status' => 'rusak_berat', 'note' => 'Tabung pecah saat pemanasan'],
                        ],
                    ],
                ],
            ],
            [
                'nim' => '2211003',
                'dosen' => '19820315',
                'days_ago' => 10,
                'durasi_hari' => 3,
                'return_after_days' => 3,
                'status' => 'Dikembalikan',
                'history' => [
                    'status_pengembalian' => 'Rusak Ringan',
                    'deskripsi' => 'Lensa mikroskop sedikit tergores dan slide perlu dibersihkan ulang.',
                ],
                'details' => [
                    [
                        'barang_kode' => 'SL-002',
                        'unit_returns' => [
                            ['status' => 'dikembalikan'],
                            ['status' => 'dikembalikan'],
                            ['status' => 'dikembalikan'],
                            ['status' => 'dikembalikan'],
                            ['status' => 'dikembalikan'],
                        ],
                    ],
                    [
                        'barang_kode' => 'MK-002',
                        'unit_returns' => [
                            ['status' => 'rusak_ringan', 'note' => 'Okuler tergores tipis'],
                        ],
                    ],
                ],
            ],
            [
                'nim' => '2211004',
                'dosen' => '19760101',
                'days_ago' => 2,
                'durasi_hari' => 5,
                'status' => 'Dipinjam',
                'details' => [
                    [
                        'barang_kode' => 'MK-003',
                        'unit_returns' => [
                            ['status' => 'belum'],
                        ],
                    ],
                    [
                        'barang_kode' => 'AP-001',
                        'unit_returns' => [
                            ['status' => 'belum'],
                            ['status' => 'belum'],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($peminjamanTemplates as $template) {
            $user = $users[$template['nim']] ?? null;
            if (!$user) {
                continue;
            }

            $dosen = $template['dosen'] ? ($dosens[$template['dosen']] ?? null) : null;
            $pinjamDate = Carbon::now()->subDays($template['days_ago'] ?? 0);
            $wajibDate = (clone $pinjamDate)->addDays($template['durasi_hari']);
            $returnDate = $template['status'] === 'Dikembalikan'
                ? (clone $pinjamDate)->addDays($template['return_after_days'] ?? $template['durasi_hari'])
                : null;

            $peminjaman = Peminjaman::create([
                'user_id' => $user->id,
                'dosen_pengampu_id' => $dosen?->id,
                'tanggal_pinjam' => $pinjamDate->toDateString(),
                'tanggal_wajib_kembali' => $wajibDate->toDateString(),
                'tanggal_kembali' => $returnDate?->toDateString(),
                'status' => $template['status'],
            ]);

            foreach ($template['details'] as $detailData) {
                $barang = $barangMap[$detailData['barang_kode']] ?? null;
                if (!$barang) {
                    continue;
                }

                $unitReturns = $detailData['unit_returns'];
                $jumlah = count($unitReturns);
                $jumlahHilang = $detailData['jumlah_rusak'] ?? count(array_filter(
                    $unitReturns,
                    fn ($unit) => $unit['status'] === 'rusak_berat'
                ));

                $detail = DetailPeminjaman::create([
                    'peminjaman_id' => $peminjaman->id,
                    'barang_id' => $barang->id,
                    'jumlah' => $jumlah,
                    'jumlah_rusak' => $jumlahHilang,
                ]);

                foreach ($unitReturns as $unitReturn) {
                    $unit = $this->popUnit($unitPools, $detailData['barang_kode']);

                    if (!$unit) {
                        continue;
                    }

                    PeminjamanUnit::create([
                        'detail_peminjaman_id' => $detail->id,
                        'barang_unit_id' => $unit->id,
                        'status_pengembalian' => $unitReturn['status'],
                        'keterangan_kondisi' => $unitReturn['note'] ?? null,
                        'foto_kondisi' => null,
                    ]);

                    $unit->status = $this->mapBarangUnitStatus($unitReturn['status'], $peminjaman->status);
                    $unit->keterangan = $unitReturn['note'] ?? $unit->keterangan;
                    $unit->save();
                }
            }

            if (($template['history'] ?? null) && $returnDate) {
                HistoryPeminjaman::create([
                    'peminjaman_id' => $peminjaman->id,
                    'user_id' => $user->id,
                    'tanggal_kembali' => $returnDate->toDateString(),
                    'status_pengembalian' => $template['history']['status_pengembalian'],
                    'deskripsi_kerusakan' => $template['history']['deskripsi'] ?? null,
                    'gambar_bukti' => $template['history']['gambar_bukti'] ?? null,
                ]);
            }
        }
    }

    protected function popUnit(array &$unitPools, string $kodeBarang): ?BarangUnit
    {
        if (empty($unitPools[$kodeBarang])) {
            return null;
        }

        /** @var BarangUnit $unit */
        $unit = array_shift($unitPools[$kodeBarang]);

        return $unit;
    }

    protected function mapBarangUnitStatus(string $unitReturnStatus, string $peminjamanStatus): string
    {
        return match ($unitReturnStatus) {
            'rusak_ringan' => 'rusak_ringan',
            'rusak_berat' => 'rusak_berat',
            'belum' => 'dipinjam',
            default => $peminjamanStatus === 'Dipinjam' ? 'dipinjam' : 'baik',
        };
    }
}
