<?php

namespace App\Services;

use App\Models\Warga;
use App\Models\LisdesCluster;

class SpatialClusteringService
{
    /**
     * Algoritma Agregasi Cluster Spasial (Distance-Based DBSCAN / Density Clustering)
     * Mengelompokkan usulan warga >200m yang berdekatan satu sama lain (< 300m antar rumah)
     * menjadi satu paketusulan Lisdes Baru.
     * 
     * @param float $epsMeters Radius jarak maksimal antar titik tetangga dalam cluster (Default 300m)
     * @param int $minPts Jumlah minimal titik untuk membentuk 1 cluster (Default 3 rumah)
     * 
     * @return array Hasil pembentukan kluster
     */
    public function generateLisdesClusters(float $epsMeters = 300.0, int $minPts = 2): array
    {
        // 1. Ambil seluruh warga yang memerlukan Lisdes (>200m dari tiang terdekat atau status lisdes)
        $wargas = Warga::whereNotNull('latitude')->whereNotNull('longitude')->get();

        $unservedNodes = [];
        foreach ($wargas as $w) {
            $nearest = SpatialEngine::findNearestPole((float)$w->latitude, (float)$w->longitude);
            $dist = $nearest ? $nearest['distance_meters'] : 9999;

            if ($dist > 200.0 || str_contains($w->status_verifikasi, 'lisdes')) {
                $unservedNodes[] = [
                    'id' => $w->id,
                    'nama' => $w->nama,
                    'desa' => $w->desa,
                    'kecamatan' => $w->kecamatan,
                    'kabupaten' => $w->kabupaten,
                    'lat' => (float)$w->latitude,
                    'lng' => (float)$w->longitude,
                    'dist_to_grid' => $dist,
                    'visited' => false,
                    'cluster_id' => null,
                ];
            }
        }

        if (empty($unservedNodes)) {
            return [
                'success' => true,
                'message' => 'Tidak ditemukan titik usulan di luar buffer 200m yang memenuhi syarat cluster.',
                'clusters_created' => 0,
            ];
        }

        // 2. Eksekusi DBSCAN Clustering Engine
        $clusterCount = 0;
        $clustersCreated = [];

        for ($i = 0; $i < count($unservedNodes); $i++) {
            if ($unservedNodes[$i]['visited']) {
                continue;
            }

            $unservedNodes[$i]['visited'] = true;
            $neighbors = $this->getRegionNeighbors($unservedNodes, $i, $epsMeters);
            
            // minPts mencakup titik itu sendiri (+ 1)
            $requiredNeighbors = max(1, $minPts - 1);

            if (count($neighbors) < $requiredNeighbors) {
                // Noise point or single node
                continue;
            }

            // Expand Cluster
            $clusterCount++;
            $clusterNodeIndices = [$i];
            $unservedNodes[$i]['cluster_id'] = $clusterCount;

            $queue = $neighbors;
            while (!empty($queue)) {
                $currIndex = array_shift($queue);
                
                if (!$unservedNodes[$currIndex]['visited']) {
                    $unservedNodes[$currIndex]['visited'] = true;
                    $currNeighbors = $this->getRegionNeighbors($unservedNodes, $currIndex, $epsMeters);
                    if (count($currNeighbors) >= $requiredNeighbors) {
                        $queue = array_merge($queue, $currNeighbors);
                    }
                }

                if ($unservedNodes[$currIndex]['cluster_id'] === null) {
                    $unservedNodes[$currIndex]['cluster_id'] = $clusterCount;
                    $clusterNodeIndices[] = $currIndex;
                }
            }

            // 3. Simpan Paket Kluster Lisdes ke Database
            $clusterNodes = array_map(fn($idx) => $unservedNodes[$idx], $clusterNodeIndices);
            
            // Hitung Centroid Lat/Lng
            $sumLat = array_sum(array_column($clusterNodes, 'lat'));
            $sumLng = array_sum(array_column($clusterNodes, 'lng'));
            $avgLat = round($sumLat / count($clusterNodes), 8);
            $avgLng = round($sumLng / count($clusterNodes), 8);

            // Hitung Estimasi Panjang Jaringan Jangkauan (Hub-and-Spoke ke Centroid)
            $totalMeter = 0.0;
            foreach ($clusterNodes as $node) {
                $totalMeter += SpatialEngine::haversineDistance($avgLat, $avgLng, $node['lat'], $node['lng']);
            }
            // Tambahkan estimasi sambungan ke grid terdekat
            $nearestGridPoint = SpatialEngine::findNearestPole($avgLat, $avgLng);
            if ($nearestGridPoint) {
                $totalMeter += $nearestGridPoint['distance_meters'];
            }

            $sampleNode = $clusterNodes[0];
            $kodeCluster = 'CLS-' . strtoupper(substr($sampleNode['desa'], 0, 3)) . '-' . sprintf('%03d', rand(10, 999));
            $estimasiBiaya = round(($totalMeter * 150000) + (count($clusterNodes) * 2500000)); // Standard cost model estimation

            $clusterModel = LisdesCluster::create([
                'kode_cluster' => $kodeCluster,
                'nama_cluster' => 'Paket Lisdes Dusun ' . $sampleNode['desa'] . ' (' . count($clusterNodes) . ' KK)',
                'desa' => $sampleNode['desa'],
                'kecamatan' => $sampleNode['kecamatan'],
                'kabupaten' => $sampleNode['kabupaten'],
                'jumlah_usulan' => count($clusterNodes),
                'total_panjang_jaringan_meter' => round($totalMeter, 2),
                'centroid_lat' => $avgLat,
                'centroid_lng' => $avgLng,
                'status' => 'draft_proposal',
                'proposal_data' => $clusterNodes,
                'estimasi_anggaran' => $estimasiBiaya,
            ]);

            $clustersCreated[] = $clusterModel;
        }

        return [
            'success' => true,
            'clusters_created' => count($clustersCreated),
            'clusters' => $clustersCreated,
            'message' => 'Berhasil membuat ' . count($clustersCreated) . ' paket agregasi cluster jaringan Lisdes baru.',
        ];
    }

    private function getRegionNeighbors(array $nodes, int $targetIdx, float $epsMeters): array
    {
        $neighbors = [];
        $target = $nodes[$targetIdx];

        for ($j = 0; $j < count($nodes); $j++) {
            if ($targetIdx === $j) continue;
            $dist = SpatialEngine::haversineDistance($target['lat'], $target['lng'], $nodes[$j]['lat'], $nodes[$j]['lng']);
            if ($dist <= $epsMeters) {
                $neighbors[] = $j;
            }
        }

        return $neighbors;
    }
}
