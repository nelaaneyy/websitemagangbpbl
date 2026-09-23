<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warga;

class DataLamaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Jalankan dengan command: php artisan db:seed --class=DataLamaSeeder
     */
    public function run(): void
    {
        $dataLama = [
            // Contoh 1: Data yang SUDAH DIREAKSISAKAN (Sesuai centang)
            [
                'nik'                      => '1571074107470324',
                'nama'                     => 'SITI JAMILAH',
                'kabupaten'                => 'KOTA JAMBI',
                'kecamatan'                => 'ALAM BARAJO',
                'desa'                     => 'BAGAN PETE',
                'rt_rw'                    => '01/01',
                'no_hp'                    => '-',
                'alamat'                   => 'JL. LINGKAR BARAT II',
                'latitude'                 => 0.0,
                'longitude'                => 0.0,
                'status_verifikasi'        => 'terpasang',
                'butuh_validasi_realisasi' => false,
                'tahun_usulan'             => '2023',
                'keterangan_import'        => null,
            ],
            // Contoh 2: Data yang TANPA CENTANG REALISASI (Masuk ke Validasi SuperAdmin)
            [
                'nik'                      => '1404112709940003',
                'nama'                     => 'SYAHRUL',
                'kabupaten'                => 'KOTA JAMBI',
                'kecamatan'                => 'ALAM BARAJO',
                'desa'                     => 'BAGAN PETE',
                'rt_rw'                    => '01/01',
                'no_hp'                    => '-',
                'alamat'                   => 'JL. SUNAN PANDARAN NO 20 RT 31',
                'latitude'                 => 0.0,
                'longitude'                => 0.0,
                'status_verifikasi'        => 'menunggu_verifikasi_pusat',
                'butuh_validasi_realisasi' => true, // Flag validasi SuperAdmin
                'tahun_usulan'             => '2023',
                'keterangan_import'        => null,
            ],
            // Contoh 3: Data Usulan 2024 (Tanpa Centang)
            [
                'nik'                      => '1571070904930001',
                'nama'                     => 'SANDI PRASETYO',
                'kabupaten'                => 'KOTA JAMBI',
                'kecamatan'                => 'ALAM BARAJO',
                'desa'                     => 'BAGAN PETE',
                'rt_rw'                    => '01/01',
                'no_hp'                    => '-',
                'alamat'                   => 'JL. R SAYUTI PERUM. PINANG MERAH',
                'latitude'                 => 0.0,
                'longitude'                => 0.0,
                'status_verifikasi'        => 'menunggu_verifikasi_pusat',
                'butuh_validasi_realisasi' => true, // Flag validasi SuperAdmin
                'tahun_usulan'             => '2024',
                'keterangan_import'        => null,
            ],
        ];

        foreach ($dataLama as $row) {
            Warga::updateOrCreate(['nik' => $row['nik']], $row);
        }
    }
}
