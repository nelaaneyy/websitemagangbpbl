<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ElectricPole;
use App\Models\Warga;
use App\Models\WorkOrder;
use App\Models\LisdesCluster;
use Illuminate\Support\Facades\Hash;

class SipelitaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed User untuk 5 Role Aktor
        $users = [
            [
                'name' => 'Super Admin ESDM',
                'email' => 'admin@esdm.go.id',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'status' => 'approved',
            ],
            [
                'name' => 'Verifikator Spasial ESDM',
                'email' => 'verifikator@esdm.go.id',
                'password' => Hash::make('password123'),
                'role' => 'verifikator_esdm',
                'status' => 'approved',
            ],
            [
                'name' => 'Kepala Desa Sidomulyo',
                'email' => 'kades@sidomulyo.desa.id',
                'password' => Hash::make('password123'),
                'role' => 'kepala_desa',
                'desa' => 'Sidomulyo',
                'status' => 'approved',
            ],
            [
                'name' => 'Staff Administrasi Desa Sidomulyo',
                'email' => 'staff@sidomulyo.desa.id',
                'password' => Hash::make('password123'),
                'role' => 'staff_desa',
                'desa' => 'Sidomulyo',
                'status' => 'approved',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['email' => $userData['email']], $userData);
        }

        // 2. Seed Sample Electric Poles (Jaringan TR/TM & Gardu)
        $poles = [
            ['kode_tiang' => 'TR-SDM-001', 'jenis' => 'TR', 'latitude' => -7.250445, 'longitude' => 112.768845, 'kapasitas_kva' => 50, 'desa' => 'Sidomulyo', 'kecamatan' => 'Krian', 'kabupaten' => 'Sidoarjo'],
            ['kode_tiang' => 'TR-SDM-002', 'jenis' => 'TR', 'latitude' => -7.251210, 'longitude' => 112.769500, 'kapasitas_kva' => 50, 'desa' => 'Sidomulyo', 'kecamatan' => 'Krian', 'kabupaten' => 'Sidoarjo'],
            ['kode_tiang' => 'TM-SDM-101', 'jenis' => 'TM', 'latitude' => -7.248900, 'longitude' => 112.765000, 'kapasitas_kva' => 100, 'desa' => 'Sidomulyo', 'kecamatan' => 'Krian', 'kabupaten' => 'Sidoarjo'],
            ['kode_tiang' => 'GD-SDM-001', 'jenis' => 'Gardu', 'latitude' => -7.249500, 'longitude' => 112.767000, 'kapasitas_kva' => 250, 'desa' => 'Sidomulyo', 'kecamatan' => 'Krian', 'kabupaten' => 'Sidoarjo'],
        ];

        foreach ($poles as $p) {
            ElectricPole::updateOrCreate(['kode_tiang' => $p['kode_tiang']], $p);
        }

        // 3. Seed Sample Lisdes Cluster
        LisdesCluster::updateOrCreate(['kode_cluster' => 'CLS-SDM-001'], [
            'nama_cluster' => 'Paket Lisdes Dusun Krajan (4 KK)',
            'desa' => 'Sidomulyo',
            'kecamatan' => 'Krian',
            'kabupaten' => 'Sidoarjo',
            'jumlah_usulan' => 4,
            'total_panjang_jaringan_meter' => 850.5,
            'centroid_lat' => -7.255000,
            'centroid_lng' => 112.775000,
            'status' => 'draft_proposal',
            'estimasi_anggaran' => 137500000,
        ]);
    }
}
