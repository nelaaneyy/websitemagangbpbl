<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Models\ActivityLog;
use App\Services\DuplicateCheckingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RedundancyResolutionController extends Controller
{
    /**
     * Tampilkan Panel Pemeriksaan Integritas & Redudansi Spasial Berdampingan (Side-by-Side Panel)
     */
    public function show(Warga $warga)
    {
        $warga->load('berkas');

        $dupService = new DuplicateCheckingService();
        $checkResult = $dupService->check(
            $warga->nik,
            (float)$warga->latitude,
            (float)$warga->longitude,
            $warga->no_kk,
            $warga->id_pelanggan,
            $warga->no_hp,
            $warga->id
        );

        $suspectRecords = $checkResult['side_by_side_records'];

        return view('dinasesdm.redundancy.show', compact('warga', 'checkResult', 'suspectRecords'));
    }

    /**
     * Eksekusi Resolusi Redudansi oleh Verifikator ESDM (UC-ESDM-RED-01 Step 5)
     */
    public function resolve(Request $request, Warga $warga)
    {
        $validated = $request->validate([
            'resolution_type' => 'required|in:reject_duplicate,merge,special_override',
            'justification_note' => 'required|string|max:1000',
            'referral_warga_id' => 'nullable|exists:wargas,id',
            'foto_sekat_fisik' => 'required_if:resolution_type,special_override|nullable|image|max:3072',
        ], [
            'justification_note.required' => 'Catatan justifikasi resolusi redudansi wajib diisi.',
            'foto_sekat_fisik.required_if' => 'Foto bukti sekat fisik independen wajib diunggah untuk Special Override.',
        ]);

        $resolutionType = $validated['resolution_type'];
        $user = auth()->user();

        if ($resolutionType === 'reject_duplicate') {
            // Option 5A: Tolak Duplikasi
            $warga->update([
                'status_verifikasi' => 'ditolak_duplikat',
                'redundancy_flag' => 'REJECTED_DUPLICATE',
                'catatan' => 'Ditolak (Duplikat): ' . $validated['justification_note'],
                'ditolak_oleh' => 'verifikator_esdm',
                'redundancy_resolution_note' => $validated['justification_note'],
            ]);

            $actionMessage = "Tolak Duplikasi (Duplikat NIK/Spasial Persil). Rujukan Ticket ID: " . ($validated['referral_warga_id'] ?? 'N/A');

        } elseif ($resolutionType === 'merge') {
            // Option 5B: Gabungkan Berkas
            $warga->update([
                'status_verifikasi' => 'digabungkan',
                'redundancy_flag' => 'MERGED',
                'catatan' => 'Berkas digabungkan ke pengajuan ID: ' . ($validated['referral_warga_id'] ?? 'N/A'),
                'redundancy_resolution_note' => $validated['justification_note'],
            ]);

            $actionMessage = "Penggabungan berkas ganda (Merge) ke Ticket ID: " . ($validated['referral_warga_id'] ?? 'N/A');

        } else {
            // Option 5C: Special Override (Beda Bangunan Sangat Rapat)
            $pathSekat = null;
            if ($request->hasFile('foto_sekat_fisik')) {
                $pathSekat = $request->file('foto_sekat_fisik')->store('redundancy/sekat_fisik', 'public');
            }

            $warga->update([
                'redundancy_flag' => 'OVERRIDDEN',
                'foto_sekat_fisik' => $pathSekat,
                'redundancy_resolution_note' => $validated['justification_note'],
                // Buka kunci kelayakan teknis dan izinkan lanjut ke buffer 200m
                'status_verifikasi' => 'menunggu_verifikasi_pusat',
            ]);

            $actionMessage = "Special Override Verifikasi Khusus (Bangunan Berhimpitan Independen). Foto bukti sekat fisik terunggah.";
        }

        // Catat ke Immutable Audit Trail Log
        ActivityLog::record(
            'resolve_redundancy',
            "Verifikator ESDM ({$user->name}) mengeksekusi resolusi redudansi [{$resolutionType}] untuk NIK {$warga->nik} ({$warga->nama}). {$actionMessage}",
            $user,
            "{$warga->latitude},{$warga->longitude}",
            [
                'warga_id' => $warga->id,
                'resolution_type' => $resolutionType,
                'redundancy_flag' => $warga->redundancy_flag,
                'justification' => $validated['justification_note'],
            ]
        );

        return redirect()->route('dinasesdm.datalist')->with('success', "Resolusi redudansi untuk pemohon {$warga->nama} berhasil disimpan!");
    }
}
