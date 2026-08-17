<?php

namespace Database\Seeders;

use App\Models\Desa;
use Illuminate\Database\Seeder;

class DesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $desas = [
            [
                'nama_desa'     => 'Mendalo Darat',
                'kabupaten'     => 'KABUPATEN MUARO JAMBI',
                'latitude'      => -1.6042,
                'longitude'     => 103.5298,
                'total_rt'      => 520,
                'berlistrik_rt' => 490,
            ],
            [
                'nama_desa'     => 'Muara Bulian',
                'kabupaten'     => 'KABUPATEN BATANG HARI',
                'latitude'      => -1.7265,
                'longitude'     => 103.2652,
                'total_rt'      => 380,
                'berlistrik_rt' => 380,
            ],
            [
                'nama_desa'     => 'Telanaipura',
                'kabupaten'     => 'KOTA JAMBI',
                'latitude'      => -1.6008,
                'longitude'     => 103.5872,
                'total_rt'      => 650,
                'berlistrik_rt' => 650,
            ],
            [
                'nama_desa'     => 'Kuala Tungkal',
                'kabupaten'     => 'KABUPATEN TANJUNG JABUNG BARAT',
                'latitude'      => -0.8197,
                'longitude'     => 103.4586,
                'total_rt'      => 410,
                'berlistrik_rt' => 310,
            ],
            [
                'nama_desa'     => 'Muara Sabak',
                'kabupaten'     => 'KABUPATEN TANJUNG JABUNG TIMUR',
                'latitude'      => -1.1352,
                'longitude'     => 103.8447,
                'total_rt'      => 290,
                'berlistrik_rt' => 180,
            ],
            [
                'nama_desa'     => 'Muara Tebo',
                'kabupaten'     => 'KABUPATEN TEBO',
                'latitude'      => -1.4875,
                'longitude'     => 102.4347,
                'total_rt'      => 340,
                'berlistrik_rt' => 240,
            ],
            [
                'nama_desa'     => 'Muara Bungo',
                'kabupaten'     => 'KABUPATEN BUNGO',
                'latitude'      => -1.4965,
                'longitude'     => 102.1287,
                'total_rt'      => 480,
                'berlistrik_rt' => 480,
            ],
            [
                'nama_desa'     => 'Sarolangun',
                'kabupaten'     => 'KABUPATEN SAROLANGUN',
                'latitude'      => -2.3023,
                'longitude'     => 102.6568,
                'total_rt'      => 310,
                'berlistrik_rt' => 200,
            ],
            [
                'nama_desa'     => 'Bangko',
                'kabupaten'     => 'KABUPATEN MERANGIN',
                'latitude'      => -2.0734,
                'longitude'     => 102.2689,
                'total_rt'      => 420,
                'berlistrik_rt' => 380,
            ],
            [
                'nama_desa'     => 'Siulak',
                'kabupaten'     => 'KABUPATEN KERINCI',
                'latitude'      => -1.9204,
                'longitude'     => 101.3265,
                'total_rt'      => 250,
                'berlistrik_rt' => 160,
            ],
            [
                'nama_desa'     => 'Sungai Penuh',
                'kabupaten'     => 'KOTA SUNGAI PENUH',
                'latitude'      => -2.0588,
                'longitude'     => 101.3908,
                'total_rt'      => 360,
                'berlistrik_rt' => 360,
            ],
            [
                'nama_desa'     => 'Renah Pembarap',
                'kabupaten'     => 'KABUPATEN MERANGIN',
                'latitude'      => -2.2150,
                'longitude'     => 101.9800,
                'total_rt'      => 190,
                'berlistrik_rt' => 0,
            ],
            [
                'nama_desa'     => 'Senyerang',
                'kabupaten'     => 'KABUPATEN TANJUNG JABUNG BARAT',
                'latitude'      => -0.9500,
                'longitude'     => 103.2100,
                'total_rt'      => 220,
                'berlistrik_rt' => 0,
            ],
        ];

        foreach ($desas as $data) {
            Desa::updateOrCreate(
                ['nama_desa' => $data['nama_desa'], 'kabupaten' => $data['kabupaten']],
                $data
            );
        }
    }
}
