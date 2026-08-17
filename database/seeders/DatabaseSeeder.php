<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun Admin Instansi ESDM
        User::create([
            'name' => 'Admin ESDM',
            'email' => 'esdm@test.com',
            'password' => bcrypt('password'),
            'role' => 'instansi',
            'desa' => null,
        ]);

        // Akun Kepala Desa
        User::create([
            'name' => 'Kepala Desa Sukamakmur',
            'email' => 'kades@test.com',
            'password' => bcrypt('password'),
            'role' => 'kepala_desa',
            'desa' => 'Sukamakmur',
        ]);

        // Seed Data Rasio Desa Provinsi Jambi
        $this->call(DesaSeeder::class);
    }
}
