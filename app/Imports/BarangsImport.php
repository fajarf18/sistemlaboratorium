<?php

namespace App\Imports;

use App\Models\Barang;
use App\Models\BarangUnit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\DB;

class BarangsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * Tentukan baris mana yang menjadi heading
     */
    public function headingRow(): int
    {
        return 1; // Header ada di row 1
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Normalisasi key dengan lebih robust
        $normalizedRow = [];
        foreach ($row as $key => $value) {
            // Hapus whitespace, lowercase, ubah spasi jadi underscore
            $normalizedKey = strtolower(trim($key));
            $normalizedKey = preg_replace('/\s+/', '_', $normalizedKey); // Replace spaces with underscore
            $normalizedRow[$normalizedKey] = $value;
        }

        // Lewati row kosong atau row yang semua valuenya null
        if (empty($normalizedRow['nama_barang']) && empty($normalizedRow['kode_barang'])) {
            return null;
        }

        // Skip row contoh (data dummy dari template)
        // Cek jika kode barang adalah contoh dari template
        $contohKode = ['A99', 'B01', 'C15'];
        if (isset($normalizedRow['kode_barang']) && in_array(trim($normalizedRow['kode_barang']), $contohKode)) {
            return null;
        }

        return DB::transaction(function () use ($normalizedRow) {
            // Buat barang
            $barang = Barang::create([
                'kode_barang' => trim($normalizedRow['kode_barang']),
                'nama_barang' => trim($normalizedRow['nama_barang']),
                'tipe' => trim($normalizedRow['tipe']),
                'stok' => (int) ($normalizedRow['stok'] ?? 0),
                'deskripsi' => !empty($normalizedRow['deskripsi']) ? trim($normalizedRow['deskripsi']) : null,
                'gambar' => null, // Gambar tidak bisa di-import
            ]);

            // Auto-generate unit barang sesuai stok
            if ($barang->stok > 0) {
                for ($i = 1; $i <= $barang->stok; $i++) {
                    BarangUnit::create([
                        'barang_id' => $barang->id,
                        'unit_code' => $barang->kode_barang . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                        'status' => 'baik',
                        'keterangan' => null,
                    ]);
                }
            }

            return $barang;
        });
    }

    /**
     * Validasi untuk setiap row
     */
    public function rules(): array
    {
        return [
            '*.kode_barang' => 'required|unique:barangs,kode_barang',
            '*.nama_barang' => 'required|string|max:255',
            '*.tipe' => 'required|in:Habis Pakai,Tidak Habis Pakai',
            '*.stok' => 'required|integer|min:0|max:1000',
        ];
    }

    /**
     * Custom error messages
     */
    public function customValidationMessages()
    {
        return [
            '*.kode_barang.required' => 'Kode barang wajib diisi.',
            '*.kode_barang.unique' => 'Kode barang :input sudah ada di database.',
            '*.nama_barang.required' => 'Nama barang wajib diisi.',
            '*.tipe.required' => 'Tipe barang wajib diisi.',
            '*.tipe.in' => 'Tipe harus "Habis Pakai" atau "Tidak Habis Pakai".',
            '*.stok.required' => 'Stok wajib diisi.',
            '*.stok.integer' => 'Stok harus berupa angka.',
            '*.stok.min' => 'Stok minimal 0.',
            '*.stok.max' => 'Stok maksimal 1000.',
        ];
    }

    /**
     * Prepare rows untuk import
     */
    public function prepareForValidation($data, $index)
    {
        // Data sudah dinormalisasi di method model()
        return $data;
    }
}
