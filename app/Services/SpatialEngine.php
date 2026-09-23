<?php

namespace App\Services;

use App\Models\ElectricPole;

class SpatialEngine
{
    /**
     * Radius Bumi rata-rata dalam meter
     */
    const EARTH_RADIUS_METERS = 6371000;

    /**
     * Hitung Jarak Presisi antara dua titik GPS (Lat1, Lng1) dan (Lat2, Lng2)
     * Menggunakan Formula Haversine
     * 
     * @return float Jarak dalam satuan Meter
     */
    public static function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round(self::EARTH_RADIUS_METERS * $c, 2);
    }

    /**
     * Cari Tiang TR/TM atau Gardu terdekat ke titik koordinat tertentu
     */
    public static function findNearestPole(float $latitude, float $longitude, ?string $jenis = null): ?array
    {
        $query = ElectricPole::aktif();
        if ($jenis) {
            $query->where('jenis', $jenis);
        }

        $poles = $query->get();
        if ($poles->isEmpty()) {
            return null;
        }

        $nearestPole = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($poles as $pole) {
            $distance = self::haversineDistance($latitude, $longitude, (float)$pole->latitude, (float)$pole->longitude);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearestPole = $pole;
            }
        }

        if (!$nearestPole) {
            return null;
        }

        return [
            'pole' => $nearestPole,
            'distance_meters' => $minDistance,
            'qualifies_bpbl' => ($minDistance <= 200), // Buffer <= 200m untuk BPBL, > 200m untuk Lisdes
        ];
    }

    /**
     * Validasi apakah lokasi berada dalam buffer radius tertentu (misal 15m proximity)
     */
    public static function isWithinProximity(float $lat1, float $lng1, float $lat2, float $lng2, float $radiusMeters = 15.0): bool
    {
        return self::haversineDistance($lat1, $lng1, $lat2, $lng2) <= $radiusMeters;
    }

    /**
     * Hitung total panjang rute jaringan (Daisy Chain / Hub and Spoke) dari susunan array koordinat
     */
    public static function calculateRouteDistance(array $coordinates): float
    {
        if (count($coordinates) < 2) {
            return 0.0;
        }

        $totalDistance = 0.0;
        for ($i = 0; $i < count($coordinates) - 1; $i++) {
            $p1 = $coordinates[$i];
            $p2 = $coordinates[$i + 1];
            $totalDistance += self::haversineDistance((float)$p1['lat'], (float)$p1['lng'], (float)$p2['lat'], (float)$p2['lng']);
        }

        return round($totalDistance, 2);
    }
}
