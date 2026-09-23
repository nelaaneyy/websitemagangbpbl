<?php

namespace App\Imports;

use App\Models\Desa; // Sesuaikan dengan Model wilayah Anda (misal: Desa / Kecamatan)
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class WilayahSheetImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    public function model(array $row)
    {
        return new Desa([
            'nama_desa' => $row['nama_desa'], // Sesuaikan key array dengan header kolom di tab Wilayah
            'kecamatan' => $row['kecamatan'],
            'kabupaten' => $row['kabupaten'],
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
