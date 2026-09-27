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
            'name' => 'Super Admin Dinas ESDM Provinsi Jambi',
            'email' => 'esdm@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'desa' => null,
        ]);

        // Akun Verifikator ESDM
        User::create([
            'name' => 'Verifikator Dinas ESDM Provinsi Jambi',
            'email' => 'verifikator@test.com',
            'password' => bcrypt('password'),
            'role' => 'verifikator',
            'desa' => null,
        ]);

        // Akun Kepala Desa
        User::create([
            'name' => 'M. Haryanto Sudarjat. S.Sos',
            'email' => 'kadessimpangrimbo@test.com',
            'password' => bcrypt('password'),
            'role' => 'kepala_desa',
            'desa' => 'Simpang Rimbo',
        ]);


        // Akun Staff Desa
        User::create([
            'name' => 'Susanti. S.Kom',
            'email' => 'staffsimpangrimbo@test.com',
            'password' => bcrypt('password'),
            'role' => 'staff_desa',
            'desa' => 'Simpang Rimbo',
        ]);

        $this->call([
            JambiWilayahSeeder::class,
            ExcelSeeder::class,
        ]);
    }
}
