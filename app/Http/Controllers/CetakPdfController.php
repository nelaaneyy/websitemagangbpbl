<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Kabupaten;
use Barryvdh\DomPDF\Facade\Pdf;


class CetakPdfController extends Controller
{
    public function cetakPerKabupaten($kabupaten_id)
    {
        $kabupaten_id = Kabupaten::with('kecamatans.desas')->find($kabupaten_id);

        $pdf = Pdf::loadView('dinasesdm.export_pdf', compact('kabupaten_id'));
        return $pdf->download("validasi_layak_bpbl_{$kabupaten_id->id}.pdf");
    }
}
