<?php

namespace App\Services;

use App\Models\Warga;
use App\Models\ActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;

class BavlGeneratorService
{
    /**
     * Generate Berita Acara Verifikasi Lapangan (BAVL) PDF Document
     */
    public function generateBavlPdf(Warga $warga)
    {
        $nomorBavl = 'BAVL/' . date('Y') . '/ESDM/' . sprintf('%05d', $warga->id);
        $tanggalSurat = now()->translatedFormat('d F Y');
        $verifikator = auth()->user() ? auth()->user()->name : 'Tim Verifikator ESDM';

        // Record Audit Log
        ActivityLog::record(
            'generate_bavl_pdf',
            "Menerbitkan Dokumen Resm Berita Acara Verifikasi Lapangan (BAVL) No. {$nomorBavl} untuk NIK {$warga->nik}",
            null,
            "{$warga->latitude},{$warga->longitude}",
            ['warga_id' => $warga->id, 'status' => $warga->status_verifikasi]
        );

        $pdf = Pdf::loadView('admin.bavl_pdf', compact('warga', 'nomorBavl', 'tanggalSurat', 'verifikator'))
            ->setPaper('a4', 'portrait');

        return $pdf;
    }
}
