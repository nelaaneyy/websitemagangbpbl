<?php

namespace App\Services;

use App\Models\Warga;

class DuplicateCheckingService
{
    /**
     * Eksekusi Multi-Layer Redundancy & Spatial Proximity Detection Engine (RULE-RED-01 s/d 03)
     * 
     * @param string $nik NIK Calon Pemohon
     * @param float $latitude Titik lokasi Lat
     * @param float $longitude Titik lokasi Lng
     * @param string|null $noKk Nomor KK Pemohon
     * @param string|null $idPelanggan ID Pelanggan / No KWH jika ada
     * @param string|null $noHp Nomor HP
     * @param int|null $ignoreWargaId ID Warga jika update/resubmit
     * 
     * @return array Hasil analisis redudansi (is_duplicate, redundancy_flag, matched_layers, details, side_by_side_records)
     */
    public function check(
        string $nik,
        float $latitude,
        float $longitude,
        ?string $noKk = null,
        ?string $idPelanggan = null,
        ?string $noHp = null,
        ?int $ignoreWargaId = null
    ): array {
        $matchedLayers = [];
        $details = [];
        $redundancyFlag = 'CLEAR';
        $riskLevel = 'rendah';
        $sideBySideRecords = [];

        // ---- RULE-RED-01: Exact Identity Collision (NIK & No. KK) ----
        $queryIdentity = Warga::where(function($q) use ($nik, $noKk) {
            $q->where('nik', $nik);
            if ($noKk) {
                $q->orWhere('no_kk', $noKk);
            }
        });

        if ($ignoreWargaId) {
            $queryIdentity->where('id', '!=', $ignoreWargaId);
        }

        $identityMatch = $queryIdentity->first();
        if ($identityMatch) {
            $redundancyFlag = 'DUPLICATE_FAMILY_CARD_OR_NIK';
            $matchedLayers[] = 'RULE-RED-01: Identity Collision (NIK / No. KK Sama)';
            $details[] = "Identitas NIK {$nik} atau KK " . ($noKk ?? '-') . " sudah terdaftar atas nama {$identityMatch->nama} (Status: {$identityMatch->status_verifikasi}).";
            $riskLevel = 'tinggi';
            $sideBySideRecords[] = $identityMatch;
        }

        // ---- RULE-RED-02: Spatial Parcel Collision (Haversine <= 15.0m) ----
        $existingWargas = Warga::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->when($ignoreWargaId, function($q, $id) {
                return $q->where('id', '!=', $id);
            })
            ->get();

        foreach ($existingWargas as $w) {
            $distance = SpatialEngine::haversineDistance($latitude, $longitude, (float)$w->latitude, (float)$w->longitude);
            if ($distance <= 15.0) { // 15.0 Meter Threshold
                if ($redundancyFlag === 'CLEAR') {
                    $redundancyFlag = 'MULTI_FAMILY_SAME_PARCEL';
                }
                $matchedLayers[] = "RULE-RED-02: Spatial Parcel Collision ({$distance}m)";
                $details[] = "Terdeteksi bangunan/persil fisik berjarak {$distance} meter (Nama: {$w->nama}, Status: {$w->status_verifikasi}).";
                if ($riskLevel !== 'tinggi') {
                    $riskLevel = 'sedang';
                }
                
                // Tambahkan ke side-by-side records jika belum ada
                if (!collect($sideBySideRecords)->pluck('id')->contains($w->id)) {
                    $sideBySideRecords[] = $w;
                }
            }
        }

        // ---- RULE-RED-03: Nearby Customer ID Collision (Radius <= 15.0m) ----
        if ($idPelanggan || !empty($sideBySideRecords)) {
            $hasEnergizedGrid = false;
            foreach ($sideBySideRecords as $record) {
                if ($record->id_pelanggan || $record->status_verifikasi === 'terpasang') {
                    $hasEnergizedGrid = true;
                    break;
                }
            }

            if ($hasEnergizedGrid || $idPelanggan) {
                $redundancyFlag = 'EXISTING_GRID_CONNECTION_DETECTED';
                $matchedLayers[] = 'RULE-RED-03: Existing Grid Meter Connection Detected';
                $details[] = 'Titik lokasi memiliki persil yang sudah teraliri listrik atau memiliki ID Pelanggan PLN eksisting.';
                $riskLevel = 'tinggi';
            }
        }

        $isDuplicate = ($redundancyFlag !== 'CLEAR');

        return [
            'is_duplicate' => $isDuplicate,
            'redundancy_flag' => $redundancyFlag,
            'risk_level' => $riskLevel,
            'matched_layers' => $matchedLayers,
            'details' => $details,
            'side_by_side_records' => $sideBySideRecords,
            'summary' => $isDuplicate ? implode(' | ', $details) : 'Bebas duplikasi & anomali spasial.',
        ];
    }
}
