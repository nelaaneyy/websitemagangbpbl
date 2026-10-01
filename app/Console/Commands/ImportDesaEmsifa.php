<?php

namespace App\Console\Commands;

use App\Models\Desa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportDesaEmsifa extends Command
{
    protected $signature = 'desa:import-emsifa {--dry-run : Hanya hitung, tidak menyimpan}';

    protected $description = 'Import desa/kelurahan Provinsi Jambi dari Emsifa v2 (data yang sudah ada tidak diubah)';

    private const BASE = 'https://www.emsifa.com/api-wilayah-indonesia/v2';

    // Kunci = nama tanpa prefix, spasi, dan tanda baca. Nilai = nama kanonik yang dipakai di tabel desas.
    private const KAB = [
        'KERINCI'               => 'KABUPATEN KERINCI',
        'MERANGIN'              => 'KABUPATEN MERANGIN',
        'SAROLANGUN'            => 'KABUPATEN SAROLANGUN',
        'BATANGHARI'            => 'KABUPATEN BATANG HARI',
        'MUAROJAMBI'            => 'KABUPATEN MUARO JAMBI',
        'TANJUNGJABUNGTIMUR'    => 'KABUPATEN TANJUNG JABUNG TIMUR',
        'TANJUNGJABUNGBARAT'    => 'KABUPATEN TANJUNG JABUNG BARAT',
        'TEBO'                  => 'KABUPATEN TEBO',
        'BUNGO'                 => 'KABUPATEN BUNGO',
        'JAMBI'                 => 'KOTA JAMBI',
        'SUNGAIPENUH'           => 'KOTA SUNGAI PENUH',
    ];

    private function fetch(string $path): array
    {
        $json = Http::retry(3, 500)->timeout(30)->get(self::BASE . $path)->throw()->json();

        return $json['data'] ?? $json ?? [];
    }

    private function clean(string $text): string
    {
        return preg_replace('/\s+/', ' ', mb_strtoupper(trim($text)));
    }

    private function normalizeKabupaten(array $reg): string
    {
        $key = preg_replace('/[^A-Z]/', '', mb_strtoupper($reg['name']));
        $key = preg_replace('/^(KABUPATEN|KAB|KOTA)/', '', $key);

        if (isset(self::KAB[$key])) {
            return self::KAB[$key];
        }

        $this->warn("Kab/kota tidak dikenal: {$reg['name']} ({$reg['id']}), memakai nama apa adanya.");

        return $this->clean($reg['name']);
    }

    public function handle(): int
    {
        $dry = $this->option('dry-run');
        $created = 0;
        $existing = 0;

        foreach ($this->fetch('/regencies/15.json') as $reg) {
            $kab = $this->normalizeKabupaten($reg);
            $this->info("Memproses {$kab} ({$reg['id']}) ...");

            foreach ($this->fetch("/districts/{$reg['id']}.json") as $kec) {
                foreach ($this->fetch("/villages/{$kec['id']}.json") as $v) {
                    $nama = $this->clean($v['name']);
                    $lat = $v['latitude'] ?? $v['lat'] ?? 0;
                    $lng = $v['longitude'] ?? $v['lng'] ?? 0;

                    if ($dry) {
                        Desa::where('nama_desa', $nama)->where('kabupaten', $kab)->exists() ? $existing++ : $created++;
                        continue;
                    }

                    $desa = Desa::unguarded(fn () => Desa::firstOrCreate(
                        ['nama_desa' => $nama, 'kabupaten' => $kab],
                        [
                            'latitude' => $lat,
                            'longitude' => $lng,
                            'total_rt' => 0,
                            'berlistrik_rt' => 0,
                            'belum_berlistrik_rt' => 0,
                            'rasio_elektrifikasi' => 0,
                            'status' => 'sebagian',
                        ]
                    ));

                    $desa->wasRecentlyCreated ? $created++ : $existing++;
                }
            }
        }

        $this->newLine();
        $this->info(($dry ? '[DRY RUN] ' : '') . "Baru: {$created} | Sudah ada: {$existing}");

        return self::SUCCESS;
    }
}