<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warga;
use App\Helpers\SimpleXlsxReader;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExcelSeeder extends Seeder
{
    public function run(): void
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        DB::disableQueryLog();

        $folderPaths = [
            database_path('seeders' . DIRECTORY_SEPARATOR . 'data'),
            database_path('data'),
        ];

        $files = [];
        foreach ($folderPaths as $folderPath) {
            if (File::exists($folderPath)) {
                $foundFiles = File::allFiles($folderPath);
                foreach ($foundFiles as $file) {
                    if (in_array(strtolower($file->getExtension()), ['xlsx', 'xls'])) {
                        $files[] = $file;
                    }
                }
            }
        }

        if (empty($files)) {
            if (isset($this->command)) {
                $this->command->error("Tidak ada file Excel (.xlsx / .xls) ditemukan di folder database/seeders/data maupun database/data!");
            }
            return;
        }

        if (isset($this->command)) {
            $this->command->info("Ditemukan " . count($files) . " file Excel. Memulai import seluruh tab/sheet data...");
        }

        $findHeaderAndRows = function(array $allRows) {
            $headerIndex = -1;
            $header = [];

            foreach ($allRows as $rIdx => $row) {
                $nonEmptyCells = array_filter($row, fn($c) => trim((string)$c) !== '');
                if (count($nonEmptyCells) < 2) {
                    continue;
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

        $totalSuccess = 0;
        $totalUpdated = 0;

        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $relativePath = $file->getRelativePathname();

            // Memanggil method penarik seluruh tab
            $allSheets = SimpleXlsxReader::parseAllSheets($filePath);
            if (empty($allSheets)) {
                continue;
            }

            // --- DETEKSI KABUPATEN DARI PATH FOLDER ---
            $normalizedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
            $parts = explode(DIRECTORY_SEPARATOR, $normalizedPath);

            $folderKab = '';
            $knownKabs = [
                'BUNGO', 'KERINCI', 'KOTA JAMBI', 'KOTA SUNGAI PENUH',
                'MERANGIN', 'SAROLANGON', 'SAROLANGUN', 'TEBO',
                'MUARO JAMBI', 'BATANGHARI', 'TANJUNG JABUNG BARAT','TANJAB BARAT',
                'TANJABBAR', 'TANJUNG JABUNG TIMUR', 'TANJAB TIMUR', 'TANJABTIM',
            ];

            foreach (array_reverse($parts) as $part) {
                $upperSegment = strtoupper(trim($part));
                foreach ($knownKabs as $kabName) {
                    if (str_contains($upperSegment, $kabName)) {
                        $folderKab = ($kabName === 'SAROLANGON') ? 'SAROLANGUN' : $kabName;
                        break 2;
                    }
                }
            }

            $fileKec = strtoupper(pathinfo($file->getFilename(), PATHINFO_FILENAME));

            // --- ITERASI SETIAP TAB / SHEET DI DALAM FILE EXCEL ---
            foreach ($allSheets as $sheetName => $parsedRows) {
                if (empty($parsedRows)) {
                    continue;
                }

                $rowsData = $findHeaderAndRows($parsedRows);
                if (empty($rowsData)) {
                    continue;
                }

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

                    // Fallback NIK
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

                    // Fallback Nama
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

// Jika NIK 17 digit akibat typo ketik angka kembar di akhir/awal, potong jadi 16 digit
if (strlen($nik) > 16) {
    $nik = substr($nik, 0, 16);
}

// Validasi Strict: NIK harus tepat 16 digit
if (empty($nik) || strlen($nik) !== 16) {
    continue; // Lewati baris data jika NIK tidak valid
}

                    $nama = $namaRaw ?: ($data['nama'] ?? 'WARGA PEMOHON');
                    $desa = $desaRaw ?: ($data['desa'] ?? 'DESA/KELURAHAN');
                    $kecamatan = (!empty($kecRaw) && $kecRaw !== '-') ? $kecRaw : ($fileKec ?: 'KECAMATAN');
                    $kabupaten = (!empty($kabRaw) && $kabRaw !== '-') ? $kabRaw : ($folderKab ?: 'KOTA JAMBI');

                    $alamat = $alamatRaw ?: ($data['alamat'] ?? ('Desa ' . $desa));
                    $usulan = $usulanRaw ?: ($data['usulan'] ?? null);
                    $realisasiVal = $realisasiRaw ?: ($data['realisasi'] ?? null);
                    $keteranganVal = $ketRaw ?: ($data['keterangan'] ?? null);

                    // Penentuan Status Realisasi
                    $valStr = trim((string)$realisasiVal);
                    $valLower = strtolower($valStr);
                    $checkIndicators = ['✓', 'v', '1', 'ya', 'sudah', 'terpasang', 'realisasi', 'true', 'ok'];

                    $isChecked = in_array($valLower, $checkIndicators, true)
                        || str_contains($valLower, '✓')
                        || str_contains($valLower, 'terpasang')
                        || str_contains($valLower, 'sudah')
                        || (is_numeric($valStr) && (int)$valStr > 2000);

                    $statusVerifikasi = 'terpasang';
                    $butuhValidasi = false;

                    $wargaData = [
                        'nik'                      => $nik,
                        'nama'                     => strtoupper($nama),
                        'kabupaten'                => strtoupper($kabupaten),
                        'kecamatan'                => strtoupper($kecamatan),
                        'desa'                     => strtoupper($desa),
                        'dusun'                    => $data['dusun'] ?? null,
                        'rt_rw'                    => $data['rt_rw'],
                        'no_hp'                    => $data['no_hp'] ?? '-',
                        'alamat'                   => $alamat,
                        'latitude'                 => is_numeric($data['latitude'] ?? null) ? (float)$data['latitude'] : 0.0,
                        'longitude'                => is_numeric($data['longitude'] ?? null) ? (float)$data['longitude'] : 0.0,
                        'status_verifikasi'        => $statusVerifikasi,
                        'butuh_validasi_realisasi' => $butuhValidasi,
                        'tahun_usulan'             => $usulan ? (string)(int)$usulan : '2023',
                        'keterangan_import'        => $keteranganVal,
                    ];

                    if ($usulan && is_numeric($usulan) && strlen((string)(int)$usulan) == 4) {
                        try {
                            $wargaData['created_at'] = Carbon::createFromDate((int)$usulan, 1, 1);
                        } catch (\Exception $e) {}
                    }

                    $existing = Warga::where('nik', $nik)->first();
                    if ($existing) {
                        $existing->update($wargaData);
                        $totalUpdated++;
                    } else {
                        Warga::create($wargaData);
                        $totalSuccess++;
                    }
                } // End foreach ($rowsData)
            } // End foreach ($allSheets)
        } // End foreach ($files)

        if (isset($this->command)) {
            $this->command->info("Selesai Impor Seluruh Tab Excel! Data Baru: {$totalSuccess}, Diperbarui: {$totalUpdated}");
        }
    }
}
