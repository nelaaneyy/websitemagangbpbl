<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;

class MultiSheetDataImport implements WithMultipleSheets, SkipsUnknownSheets
{
    public function sheets(): array
    {
        return [
            'Pemohon' => new PemohonSheetImport(),
            'Wilayah' => new WilayahSheetImport(),
        ];
    }

    public function onUnknownSheet($sheetName)
    {
        // Skip sheet yang tidak terdaftar
    }
}
