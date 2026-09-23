<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FixMissingCoordinates extends Command
{
    /**
     * Perintah artisan yang dipanggil di terminal
     *
     * @var string
     */
    protected $signature = 'gis:fix-coordinates';

    /**
     * Deskripsi singkat perintah
     *
     * @var string
     */
    protected $description = 'Melengkapi koordinat desa/kelurahan yang masih bernilai 0.0 di tabel desas';

    /**
     * Tempat menaruh logika eksekusi utama
     */
    public function handle(): int
    {
        // 1. Ambil data desa yang latitude-nya masih 0
        $missingDesas = DB::table('desas')->where('latitude', 0)->get();

        if ($missingDesas->isEmpty()) {
            $this->info("✨ Semua desa sudah memiliki koordinat! Tidak ada data bernilai 0.0.");
            return Command::SUCCESS;
        }

        $this->info("Ditemukan {$missingDesas->count()} desa tanpa koordinat. Memulai pencarian ulang...");

        $headers = [
            'User-Agent' => 'ELISTRIK-WebGIS-ESDM-PROV-JAMBI/1.0 (dev-agnela_amisha_dewi, agnelaamishadewi@gmail.com)'
        ];

        $fixedCount = 0;

        foreach ($missingDesas as $desa) {
            // Cleaning string nama tempat agar lebih ramah bagi OpenStreetMap
            $cleanDesa = trim(preg_replace('/^(Desa|Kelurahan)\s+/i', '', $desa->nama_desa));
            $cleanKab  = trim(preg_replace('/^(Kabupaten|Kota)\s+/i', '', $desa->kabupaten));

            // Query pencarian fleksibel (Tanpa kecamatan agar lebih broad)
            // Tambahkan variasi kata 'Kelurahan' dan penulisan gabung/pisah
$queries = [
    "Kelurahan {$cleanDesa}, Sungai Penuh, Jambi",
    "{$cleanDesa}, Sungai Penuh, Jambi, Indonesia",
    "Desa {$cleanDesa}, Sungai Penuh, Jambi",
    "{$cleanDesa}, Kerinci, Jambi" // Beberapa desa lama Sungai Penuh masih terindeks bawah Kerinci di OSM
];

            $found = false;

            foreach ($queries as $query) {
                try {
                    $response = Http::retry(2, 1000)
                        ->withHeaders($headers)
                        ->timeout(10)
                        ->get("https://nominatim.openstreetmap.org/search", [
                            'q'            => $query,
                            'format'       => 'json',
                            'limit'        => 1,
                            'countrycodes' => 'id',
                        ]);

                    if ($response->successful() && !empty($response->json())) {
                        $data = $response->json()[0];

                        // Update koordinat di database
                        DB::table('desas')->where('id', $desa->id)->update([
                            'latitude'   => (float) $data['lat'],
                            'longitude'  => (float) $data['lon'],
                            'updated_at' => now(),
                        ]);

                        $fixedCount++;
                        $this->info("✅ Fixed [{$fixedCount}/{$missingDesas->count()}]: {$desa->nama_desa} ({$cleanKab}) -> Lat: {$data['lat']}, Lon: {$data['lon']}");
                        $found = true;
                        break; // Stop loop query jika sudah ketemu
                    }
                } catch (\Exception $e) {
                    Log::warning("Error geocoding fallback pada desa {$desa->nama_desa}: " . $e->getMessage());
                }

                usleep(500000); // Delay 0.5 detik antar percobaan query
            }

            if (!$found) {
                $this->warn("⚠️ Masih gagal: {$desa->nama_desa} ({$cleanKab})");
            }

            // Delay wajib 1.1 detik per desa (Aturan TOS Nominatim)
            usleep(1100000);
        }

        $this->info("Selesai! Berhasil memperbaiki {$fixedCount} dari {$missingDesas->count()} desa.");
        return Command::SUCCESS;
    }
}
