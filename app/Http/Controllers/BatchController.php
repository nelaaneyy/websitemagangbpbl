<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\PengajuanBatch; 

class BatchController extends Controller
{
    public function kirimBatchKeKades(PengajuanBatch $batch)
    {
        // 1. Update status batch dan hitung total warga
        $batch->update([
            'status'      => 'dikirim_ke_kades',
            'total_warga' => $batch->wargas()->count(),
        ]);

        // 2. Catat audit trail di Activity Log
        ActivityLog::record(
            'staff_submit_batch_kades',
            'Staff Desa meneruskan usulan Batch ' . $batch->kode_batch . ' (' . $batch->total_warga . ' warga) ke Kepala Desa ' . $batch->desa
        );

        // 3. Kembalikan respons sukses ke halaman sebelumnya
        return back()->with('success', 'Batch ' . $batch->kode_batch . ' berhasil dikirim ke Kepala Desa untuk divalidasi!');
    }

    public function createFromDraft(Request $request)
    {
        return redirect()->route('staffdesa.index')->with('info', 'Batch telah aktif.');
    }

    public function reviewForm($id)
    {
        return redirect()->route('kepaladesa.index');
    }

    public function approveAndSendToEsdm(Request $request, $id)
    {
        $batch = PengajuanBatch::findOrFail($id);
        return app(\App\Http\Controllers\KepalaDesa\KepalaDesaController::class)->submitBatchToEsdm($request, $batch);
    }
}