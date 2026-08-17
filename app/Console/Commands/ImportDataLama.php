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
    protected $signature = 'import:data-lama {file : Path file CSV/Excel yang akan diimport} {--status=lolos_verifikasi_pusat : Default status verifikasi}';

    /**
     * Deskripsi command CLI.
     */
    protected $description = 'Mengimpor data penerima/pengajuan BPBL real dari file CSV/Excel ke database';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan pada path: {$filePath}");
            return 1;
        }

        $this->info("Memulai proses import data dari file: {$filePath}");

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            $this->error("Gagal membuka file: {$filePath}");
            return 1;
        }

        // Read header row
        $header = fgetcsv($handle, 2000, ',');
        $delimiter = ',';

        if ($header && count($header) == 1 && strpos($header[0], ';') !== false) {
            rewind($handle);
            $header = fgetcsv($handle, 2000, ';');
            $delimiter = ';';
        }

        if (!$header) {
            $this->error("File kosong atau header tidak valid.");
            fclose($handle);
            return 1;
        }

        $header = array_map(function($h) {
            return strtolower(trim(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $h)));
        }, $header);

        $successCount = 0;
        $updatedCount = 0;
        $failedCount  = 0;
        $defaultStatus = $this->option('status');

        $rows = [];
        while (($row = fgetcsv($handle, 3000, $delimiter)) !== false) {
            if (count($row) >= 2 && !empty(array_filter($row))) {
                $rows[] = $row;
            }
        }
        fclose($handle);

        $this->info("Ditemukan " . count($rows) . " baris data. Memproses...");

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            $data = [];
            foreach ($header as $index => $colName) {
                $data[$colName] = isset($row[$index]) ? trim($row[$index]) : null;
            }

            $nik = preg_replace('/[^0-9]/', '', $data['nik'] ?? '');
            $nama = $data['nama'] ?? null;
            $kabupaten = $data['kabupaten'] ?? null;
            $kecamatan = $data['kecamatan'] ?? null;
            $desa = $data['desa'] ?? null;

            if (empty($nik) || strlen($nik) < 10 || empty($nama) || empty($desa)) {
                $failedCount++;
                $bar->advance();
                continue;
            }

            $statusVerifikasi = !empty($data['status_verifikasi']) 
                ? $data['status_verifikasi'] 
                : $defaultStatus;

            $tanggalPengajuan = !empty($data['tanggal_pengajuan']) 
                ? $data['tanggal_pengajuan'] 
                : null;

            $wargaData = [
                'nik'               => $nik,
                'nama'              => $nama,
                'kabupaten'         => $kabupaten ?: 'KABUPATEN MUARO JAMBI',
                'kecamatan'         => $kecamatan ?: '-',
                'desa'              => $desa,
                'dusun'             => $data['dusun'] ?? ($data['nama_dusun'] ?? null),
                'rt_rw'             => $data['rt_rw'] ?? '01/01',
                'no_hp'             => $data['no_hp'] ?? '-',
                'alamat'            => $data['alamat'] ?? 'Desa ' . $desa,
                'jarak_tiang'       => $data['jarak_tiang'] ?? null,
                'latitude'          => is_numeric($data['latitude'] ?? null) ? $data['latitude'] : 0.0,
                'longitude'         => is_numeric($data['longitude'] ?? null) ? $data['longitude'] : 0.0,
                'status_verifikasi' => $statusVerifikasi,
            ];

            if ($tanggalPengajuan) {
                try {
                    $wargaData['created_at'] = Carbon::parse($tanggalPengajuan);
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
        $this->info("Data Baru Ditambahkan : {$successCount}");
        $this->info("Data Diperbarui (NIK)  : {$updatedCount}");
        if ($failedCount > 0) {
            $this->warn("Baris Dilewati (Invalid) : {$failedCount}");
        }
        $this->info("==========================================");

        return 0;
    }
}
