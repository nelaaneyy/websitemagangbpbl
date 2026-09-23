<?php

namespace App\Services;

use App\Models\ElectricPole;
use Illuminate\Http\UploadedFile;

class ElectricPoleImportService
{
    /**
     * Parse and import file (GeoJSON, JSON, or CSV) into electric_poles table
     */
    public function import(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $importedCount = 0;
        $errors = [];

        if (in_array($extension, ['json', 'geojson'])) {
            $content = file_get_contents($file->getRealPath());
            $data = json_decode($content, true);

            if (isset($data['type']) && $data['type'] === 'FeatureCollection') {
                foreach ($data['features'] as $index => $feature) {
                    try {
                        $props = $feature['properties'] ?? [];
                        $coords = $feature['geometry']['coordinates'] ?? [0, 0];

                        // GeoJSON format is [longitude, latitude]
                        $lng = $coords[0] ?? 0;
                        $lat = $coords[1] ?? 0;

                        ElectricPole::updateOrCreate(
                            ['kode_tiang' => $props['kode_tiang'] ?? 'PL-' . sprintf('%04d', rand(1000, 9999))],
                            [
                                'jenis'         => $props['jenis'] ?? 'TR',
                                'latitude'      => $lat,
                                'longitude'     => $lng,
                                'kapasitas_kva' => $props['kapasitas_kva'] ?? 0,
                                'status'        => $props['status'] ?? 'aktif',
                                'kondisi'       => $props['kondisi'] ?? 'baik',
                                'alamat'        => $props['alamat'] ?? null,
                                'desa'          => $props['desa'] ?? null,
                                'kecamatan'     => $props['kecamatan'] ?? null,
                                'kabupaten'     => $props['kabupaten'] ?? null,
                            ]
                        );
                        $importedCount++;
                    } catch (\Exception $e) {
                        $errors[] = "Feature #{$index}: " . $e->getMessage();
                    }
                }
            }
        } elseif (in_array($extension, ['csv', 'txt'])) {
            $handle = fopen($file->getRealPath(), 'r');
            $header = fgetcsv($handle, 1000, ',');
            
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($row) < 3) continue;

                try {
                    $kode = $row[0] ?? 'PL-' . sprintf('%04d', rand(1000, 9999));
                    $jenis = $row[1] ?? 'TR';
                    $lat = (float)($row[2] ?? 0);
                    $lng = (float)($row[3] ?? 0);

                    ElectricPole::updateOrCreate(
                        ['kode_tiang' => $kode],
                        [
                            'jenis' => $jenis,
                            'latitude' => $lat,
                            'longitude' => $lng,
                            'status' => 'aktif',
                        ]
                    );
                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "CSV Row error: " . $e->getMessage();
                }
            }
            fclose($handle);
        }

        return [
            'success' => true,
            'imported_count' => $importedCount,
            'errors' => $errors,
        ];
    }

    /**
     * Export all electric poles as a FeatureCollection GeoJSON array
     */
    public function exportGeoJson(): array
    {
        $poles = ElectricPole::all();

        $features = $poles->map(function ($pole) {
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float)$pole->longitude, (float)$pole->latitude],
                ],
                'properties' => [
                    'id' => $pole->id,
                    'kode_tiang' => $pole->kode_tiang,
                    'jenis' => $pole->jenis,
                    'status' => $pole->status,
                    'kondisi' => $pole->kondisi,
                    'kapasitas_kva' => $pole->kapasitas_kva,
                    'alamat' => $pole->alamat,
                    'desa' => $pole->desa,
                ],
            ];
        });

        return [
            'type' => 'FeatureCollection',
            'features' => $features->toArray(),
        ];
    }
}
