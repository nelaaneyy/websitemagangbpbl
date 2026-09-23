<?php

namespace App\Imports;

use App\Models\Warga; // Sesuaikan dengan Model tujuan (misal: Warga / Pemohon)
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class PemohonSheetImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    protected $kapubaten;

    public function __construct($kapubaten = null)
    {
        $this->kapubaten = $kapubaten;
    }
    public function model(array $row)
{
    return new Warga([
        'nik'               => $row['nik'] ?? null,
        'nama'              => $row['nama'] ?? null,
        'alamat'            => $row['alamat'] ?? null,
        'kapubaten'          => $this->kapubaten,
        'status_verifikasi' => 'Lolos Verifikasi Pusat', // atau disesuaikan dengan value enum/string di database Anda
        'sumber_data'       => 'Data Historis Usulan BPBL',
        'tahun'             => 2023, // atau sesuaikan dengan tahun data
    ]);
}

    public function chunkSize(): int
    {
        return 1000; // Membaca data per 1000 baris
    }

    public function batchSize(): int
    {
        return 1000; // Insert ke database per 1000 baris
    }
}
