<?php

namespace App\Services;

use App\Models\PengajuanLisdes;
use App\Models\LisdesCluster;
use Barryvdh\DomPDF\Facade\Pdf;

class LisdesMapPdfService
{
    /**
     * Generate Cartographic Lisdes Map PDF Layout with embedded QR Code & calculations
     */
    public function generateMapPdf($lisdesIdOrClusterId, string $type = 'lisdes')
    {
        if ($type === 'cluster') {
            $data = LisdesCluster::findOrFail($lisdesIdOrClusterId);
            $title = "PETA LAYOUT KARTOGRAFI CLUSTER LISDES - " . $data->kode_cluster;
        } else {
            $data = PengajuanLisdes::findOrFail($lisdesIdOrClusterId);
            $title = "PETA KARTOGRAFI USULAN LISDES DESA " . strtoupper($data->desa);
        }

        $pdf = Pdf::loadView('dinasesdm.lisdes_map_pdf', compact('data', 'type', 'title'))
            ->setPaper('a4', 'landscape');

        return $pdf;
    }
}
