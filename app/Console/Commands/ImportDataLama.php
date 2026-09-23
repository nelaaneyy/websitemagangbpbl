<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Warga;
use Carbon\Carbon;

class ImportDataLama extends Command
{
    /**
     * Nama dan signature dari command CLI.
     * Contoh penggunaan: php artisan import:data-lama path/to/file.csv
     */
    protected $signature = 'import:data-lama {file : Path file CSV/Excel yang akan diimport} {--status= : Force override status verifikasi}';

    /**
     * Deskripsi command CLI.
     */
    protected $description = 'Mengimpor data penerima/pengajuan BPBL real dari file CSV/Excel ke database (format No, KECAMATAN, DESA/KELURAHAN, NAMA, NIK, ALAMAT, Usulan, Realisasi, Keterangan)';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan pada path: {$filePath}");
            return 1;
        }

        $this->info("Memulai proses import data dari file: {$filePath}");

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $rowsData = [];

        $findHeaderAndRows = function(array $allRows) {
            $headerIndex = -1;
            $header = [];

            foreach ($allRows as $rIdx => $row) {
                $nonEmptyCells = array_filter($row, fn($c) => trim((string)$c) !== '');
                if (count($nonEmptyCells) < 2) {
                    continue; // Skip single-cell title headers
                }

                $rowJoined = strtolower(implode(' | ', array_map('strval', $row)));
                
                $keywords = ['nik', 'nama', 'kecamatan', 'desa', 'kelurahan', 'alamat', 'realisasi', 'usulan'];
                $matchCount = 0;
                foreach ($keywords as $kw) {
                    if (str_contains($rowJoined, $kw)) {
                        $matchCount++;
                    }
                }

                if ($matchCount >= 2) {
                    $headerIndex = $rIdx;
                    $header = array_map(function($h) {
                        return mb_strtolower(trim(preg_replace('/[\x00-\x1F\x7F]/', '', (string)$h)));
                    }, $row);
                    break;
                }
            }

            if ($headerIndex === -1 && !empty($allRows)) {
                foreach ($allRows as $rIdx => $row) {
                    $nonEmptyCells = array_filter($row, fn($c) => trim((string)$c) !== '');
                    if (count($nonEmptyCells) >= 3) {
                        $headerIndex = $rIdx;
                        $header = array_map(function($h) {
                            return mb_strtolower(trim(preg_replace('/[\x00-\x1F\x7F]/', '', (string)$h)));
                        }, $row);
                        break;
                    }
                }
            }

            if ($headerIndex === -1 && !empty($allRows)) {
                $headerIndex = 0;
                $header = array_map(function($h) {
                    return mb_strtolower(trim(preg_replace('/[\x00-\x1F\x7F]/', '', (string)$h)));
                }, $allRows[0]);
            }

            $dataRows = [];
            for ($i = $headerIndex + 1; $i < count($allRows); $i++) {
                $row = $allRows[$i];
                if (empty(array_filter($row, fn($c) => trim((string)$c) !== ''))) continue;
                $data = [];
                foreach ($header as $idx => $colName) {
                    $data[$colName] = isset($row[$idx]) ? trim((string)$row[$idx]) : '';
                }
                $data['_raw_cols_'] = $row;
                $dataRows[] = $data;
            }
            return $dataRows;
        };

        if (in_array($extension, ['xlsx', 'xls'])) {
            $parsedRows = \App\Helpers\SimpleXlsxReader::parse($filePath);
            if (!empty($parsedRows)) {
                $rowsData = $findHeaderAndRows($parsedRows);
            }
        }

        if (empty($rowsData)) {
            $handle = fopen($filePath, 'r');
            if ($handle === false) {
                $this->error("Gagal membuka file: {$filePath}");
                return 1;
            }

            $allRawRows = [];
            $delimiter = ',';
            $firstLine = fgetcsv($handle, 3000, ',');
            if ($firstLine && count($firstLine) == 1 && strpos($firstLine[0], ';') !== false) {
                $delimiter = ';';
            }
            rewind($handle);

            while (($row = fgetcsv($handle, 3000, $delimiter)) !== false) {
                $allRawRows[] = $row;
            }
            fclose($handle);

            if (!empty($allRawRows)) {
                $rowsData = $findHeaderAndRows($allRawRows);
            }
        }

        $successCount = 0;
        $updatedCount = 0;
        $needValidationCount = 0;
        $failedCount  = 0;
        $forcedStatus = $this->option('status');

        $this->info("Ditemukan " . count($rowsData) . " baris data. Memproses...");

        $bar = $this->output->createProgressBar(count($rowsData));
        $bar->start();

        foreach ($rowsData as $data) {
            $nikRaw = '';
            $namaRaw = '';
            $desaRaw = '';
            $kecRaw = '';
            $kabRaw = '';
            $alamatRaw = '';
            $usulanRaw = '';
            $realisasiRaw = '';
            $ketRaw = '';

            foreach ($data as $colKey => $cellVal) {
                if ($colKey === '_raw_cols_') continue;
                $kClean = strtolower(trim((string)$colKey));
                $vClean = trim((string)$cellVal);

                if ($vClean === '') continue;

                if (empty($nikRaw) && str_contains($kClean, 'nik')) {
                    $nikRaw = $vClean;
                } elseif (empty($namaRaw) && str_contains($kClean, 'nama')) {
                    $namaRaw = $vClean;
                } elseif (empty($desaRaw) && (str_contains($kClean, 'desa') || str_contains($kClean, 'kelurahan'))) {
                    $desaRaw = $vClean;
                } elseif (empty($kecRaw) && (str_contains($kClean, 'kecamatan') || str_contains($kClean, 'kec'))) {
                    $kecRaw = $vClean;
                } elseif (empty($kabRaw) && str_contains($kClean, 'kabupaten')) {
                    $kabRaw = $vClean;
                } elseif (empty($alamatRaw) && str_contains($kClean, 'alamat')) {
                    $alamatRaw = $vClean;
                } elseif (empty($usulanRaw) && (str_contains($kClean, 'usulan') || str_contains($kClean, 'tahun'))) {
                    $usulanRaw = $vClean;
                } elseif (empty($realisasiRaw) && str_contains($kClean, 'realisasi')) {
                    $realisasiRaw = $vClean;
                } elseif (empty($ketRaw) && (str_contains($kClean, 'keterangan') || str_contains($kClean, 'catatan'))) {
                    $ketRaw = $vClean;
                }
            }

            // Fallback for NIK if empty
            if (empty($nikRaw) && isset($data['_raw_cols_'])) {
                foreach ($data['_raw_cols_'] as $cVal) {
                    $vStr = trim((string)$cVal);
                    if ($vStr === '') continue;
                    $numOnly = preg_replace('/[^0-9]/', '', $vStr);
                    if (strlen($numOnly) >= 10 && strlen($numOnly) <= 18) {
                        $nikRaw = $vStr;
                        break;
                    }
                }
            }

            // Fallback for Nama if empty
            if (empty($namaRaw) && isset($data['_raw_cols_'])) {
                foreach ($data['_raw_cols_'] as $cVal) {
                    $vStr = trim((string)$cVal);
                    if ($vStr === '') continue;
                    if (!preg_match('/[0-9]/', $vStr) && strlen($vStr) >= 3) {
                        $vLower = strtolower($vStr);
                        if (!in_array($vLower, ['✓', 'v', 'ya', 'tidak', 'sudah', 'belum', 'terpasang'])) {
                            $namaRaw = $vStr;
                            break;
                        }
                    }
                }
            }

            $nikClean = str_replace(',', '.', (string)$nikRaw);
            if (is_numeric($nikClean) && str_contains(strtolower($nikClean), 'e+')) {
                $nik = sprintf('%.0f', (float)$nikClean);
            } else {
                $nik = preg_replace('/[^0-9]/', '', (string)$nikRaw);
            }

            $nama = $namaRaw ?: ($data['nama'] ?? 'WARGA PEMOHON');
            $desa = $desaRaw ?: ($data['desa'] ?? 'BAGAN PETE');
            $kecamatan = (!empty($kecRaw) && $kecRaw !== '-') ? $kecRaw : ($data['kecamatan'] ?? 'ALAM BARAJO');
            $kabupaten = (!empty($kabRaw) && $kabRaw !== '-' && $kabRaw !== 'KABUPATEN MUARO JAMBI') ? $kabRaw : ($data['kabupaten'] ?? 'KOTA JAMBI');
            if (strtoupper($kecamatan) === 'ALAM BARAJO') {
                $kabupaten = 'KOTA JAMBI';
            }
            $alamat = $alamatRaw ?: ($data['alamat'] ?? ('Desa ' . $desa));
            $usulan = $usulanRaw ?: ($data['usulan'] ?? null);
            $realisasiVal = $realisasiRaw ?: ($data['realisasi'] ?? null);
            $keteranganVal = $ketRaw ?: ($data['keterangan'] ?? null);

            if (empty($nik) || strlen($nik) < 10) {
                $failedCount++;
                $bar->advance();
                continue;
            }

            $realisasiResult = $this->parseRealisasiStatus($realisasiVal);
            $statusVerifikasi = $forcedStatus ?: $realisasiResult['status_verifikasi'];
            $butuhValidasi = $forcedStatus ? false : $realisasiResult['butuh_validasi_realisasi'];

            if ($butuhValidasi) {
                $needValidationCount++;
            }

            $wargaData = [
                'nik'                      => $nik,
                'nama'                     => $nama,
                'kabupaten'                => $kabupaten,
                'kecamatan'                => $kecamatan,
                'desa'                     => $desa,
                'dusun'                    => $data['dusun'] ?? null,
                'rt_rw'                    => $data['rt_rw'] ?? '01/01',
                'no_hp'                    => $data['no_hp'] ?? '-',
                'alamat'                   => $alamat,
                'latitude'                 => is_numeric($data['latitude'] ?? null) ? (float)$data['latitude'] : 0.0,
                'longitude'                => is_numeric($data['longitude'] ?? null) ? (float)$data['longitude'] : 0.0,
                'status_verifikasi'        => $statusVerifikasi,
                'butuh_validasi_realisasi' => $butuhValidasi,
                'tahun_usulan'             => $usulan ? (string) $usulan : null,
                'keterangan_import'        => $keteranganVal,
            ];

            if ($usulan && is_numeric($usulan) && strlen((string)$usulan) == 4) {
                try {
                    $wargaData['created_at'] = Carbon::createFromDate((int)$usulan, 1, 1);
                } catch (\Exception $e) {}
            }

            $existing = Warga::where('nik', $nik)->first();
            if ($existing) {
                $existing->update($wargaData);
                $updatedCount++;
            } else {
                Warga::create($wargaData);
                $successCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("==========================================");
        $this->info("IMPORT SELESAI!");
        $this->info("Data Baru Ditambahkan            : {$successCount}");
        $this->info("Data Diperbarui (NIK)             : {$updatedCount}");
        $this->warn("Data Perlu Validasi SuperAdmin    : {$needValidationCount}");
        if ($failedCount > 0) {
            $this->error("Baris Dilewati (Invalid)          : {$failedCount}");
        }
        $this->info("==========================================");

        return 0;
    }

    /**
     * Helper untuk memeriksa centang/isi pada kolom Realisasi
     */
    private function parseRealisasiStatus($realisasiVal): array
    {
        $val = trim((string) $realisasiVal);

        if ($val === '') {
            return [
                'status_verifikasi' => 'menunggu_verifikasi_pusat',
                'butuh_validasi_realisasi' => true,
            ];
        }

        $valLower = strtolower($val);
        $checkIndicators = ['✓', 'v', '1', 'ya', 'sudah', 'terpasang', 'realisasi', 'true', 'ok'];

        $isChecked = in_array($valLower, $checkIndicators, true)
            || str_contains($valLower, '✓')
            || str_contains($valLower, 'terpasang')
            || str_contains($valLower, 'sudah')
            || (is_numeric($val) && (int)$val > 2000);

        if ($isChecked) {
            return [
                'status_verifikasi' => 'terpasang',
                'butuh_validasi_realisasi' => false,
            ];
        }

        return [
            'status_verifikasi' => 'menunggu_verifikasi_pusat',
            'butuh_validasi_realisasi' => true,
        ];
    }
}
