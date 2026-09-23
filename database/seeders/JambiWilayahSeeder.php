<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JambiWilayahSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info("1. Memulai Fetch Data Wilayah Provinsi Jambi (15) dari Emsifa...");

        $provId = "15";
        $baseUrl = "https://www.emsifa.com/api-wilayah-indonesia/api";

        $headers = [
            'User-Agent' => 'ELISTRIK-WebGIS-ESDM-PROV-JAMBI/1.0 (dev-agnela_amisha_dewi, agnelaamishadewi@gmail.com)'
        ];

        // 1. Ambil seluruh Kabupaten/Kota di Provinsi Jambi
        $regenciesResponse = Http::withHeaders($headers)->get("{$baseUrl}/regencies/{$provId}.json");

        if ($regenciesResponse->failed()) {
            $this->command->error("Gagal mengambil data kabupaten/kota Provinsi Jambi.");
            return;
        }

        $regencies = $regenciesResponse->json();
        $totalDesaProses = 0;

        $this->command->info("2. Memproses Geocoding Presisi untuk Setiap Desa di Provinsi Jambi...\n");

        foreach ($regencies as $reg) {
            $regId   = $reg['id'];   // Format: "1501"
            $regName = $reg['name']; // Format: "KABUPATEN KERINCI"

            $this->command->warn("=== Memproses {$regName} (ID: {$regId}) ===");

            // 2. Ambil seluruh Kecamatan per Kabupaten
            $districtsResponse = Http::withHeaders($headers)->get("{$baseUrl}/districts/{$regId}.json");

            if ($districtsResponse->failed()) {
                $this->command->error("Gagal mengambil kecamatan untuk {$regName}");
                continue;
            }

            $districts = $districtsResponse->json();

            foreach ($districts as $dist) {
                $distId   = $dist['id'];
                $distName = $dist['name'];

                // 3. Ambil seluruh Desa/Kelurahan per Kecamatan
                $villagesResponse = Http::withHeaders($headers)->get("{$baseUrl}/villages/{$distId}.json");

                if ($villagesResponse->failed()) {
                    continue;
                }

                $villages = $villagesResponse->json();

                foreach ($villages as $vil) {
                    $namaDesa = $vil['name'];

                    // Geocoding Presisi via Nominatim OSM
                    $coords = $this->geocodeVillage($namaDesa, $distName, $regName, $headers);

                    // UPDATE ATAU INSERT SESUAI KOLOM MIGRATION DESAS
                    DB::table('desas')->updateOrInsert(
                        [
                            // Unik berdasarkan nama_desa dan kabupaten
                            'nama_desa' => $namaDesa,
                            'kabupaten' => $regName,
                        ],
                        [
                            'latitude'   => $coords['lat'],
                            'longitude'  => $coords['lon'],
                            'updated_at' => now(),
                            'created_at' => DB::raw('COALESCE(created_at, NOW())')
                        ]
                    );

                    $totalDesaProses++;
                    $statusIcon = ($coords['lat'] != 0.0) ? "✅" : "⚠️";
                    $this->command->line("  {$statusIcon} [{$totalDesaProses}] {$namaDesa} ({$distName}, {$regName}) -> Lat: {$coords['lat']}, Long: {$coords['lon']}");

                    // Delay 1.1 detik per desa (TOS Nominatim)
                    usleep(1100000);
                }
            }
        }

        $this->command->info("\n🎉 Selesai! Total {$totalDesaProses} desa di Provinsi Jambi berhasil diperbarui koordinatnya.");
    }

    /**
     * Helper Method: Geocoding Nominatim OSM
     */
    private function geocodeVillage(string $namaDesa, string $distName, string $regName, array $headers): array
    {
        $cleanDesa = trim(preg_replace('/^(Desa|Kelurahan)\s+/i', '', $namaDesa));
        $cleanKec  = trim(preg_replace('/^Kecamatan\s+/i', '', $distName));
        $cleanKab  = trim(preg_replace('/^(Kabupaten|Kota)\s+/i', '', $regName));

        $queries = [
            "{$cleanDesa}, {$cleanKec}, {$cleanKab}, Jambi, Indonesia",
            "Desa {$cleanDesa}, {$cleanKec}, Jambi",
            "{$cleanDesa}, {$cleanKab}, Jambi",
            "Kecamatan {$cleanKec}, {$cleanKab}, Jambi"
        ];

        foreach ($queries as $query) {
            try {
                $response = Http::retry(3, 1500)
                    ->withHeaders($headers)
                    ->timeout(10)
                    ->get("https://nominatim.openstreetmap.org/search", [
                        'q'            => $query,
                        'format'       => 'json',
                        'limit'        => 1,
                        'countrycodes' => 'id',
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data)) {
                        return [
                            'lat' => (float) $data[0]['lat'],
                            'lon' => (float) $data[0]['lon'],
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Geocoding timeout/error pada query: {$query}");
            }

            usleep(500000);
        }

        return ['lat' => 0.0, 'lon' => 0.0];
    }
}
