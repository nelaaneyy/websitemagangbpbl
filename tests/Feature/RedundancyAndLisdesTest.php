<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Warga;
use App\Services\DuplicateCheckingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class RedundancyAndLisdesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function five_roles_are_correctly_identified()
    {
        $warga = User::create(['name' => 'Warga', 'email' => 'warga@test.com', 'password' => 'password', 'role' => 'warga']);
        $staff = User::create(['name' => 'Staff', 'email' => 'staff@test.com', 'password' => 'password', 'role' => 'staff_desa']);
        $kades = User::create(['name' => 'Kades', 'email' => 'kades@test.com', 'password' => 'password', 'role' => 'kepala_desa']);
        $verif = User::create(['name' => 'Verif', 'email' => 'verif@test.com', 'password' => 'password', 'role' => 'verifikator_esdm']);
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => 'password', 'role' => 'super_admin']);

        $this->assertTrue($warga->isWarga());
        $this->assertTrue($staff->isStaffDesa());
        $this->assertTrue($kades->isKepalaDesa());
        $this->assertTrue($verif->isVerifikatorEsdm());
        $this->assertTrue($admin->isAdminEsdm());
    }

    /** @test */
    public function redundancy_engine_detects_rule_red_01_and_02()
    {
        // Warga 1 (Existing)
        $w1 = Warga::create([
            'nik' => '3515010101010001',
            'no_kk' => '3515010101019999',
            'nama' => 'Budi',
            'kabupaten' => 'Sidoarjo',
            'kecamatan' => 'Krian',
            'desa' => 'Sidomulyo',
            'rt_rw' => '01/01',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Raya No. 1',
            'latitude' => -7.250000,
            'longitude' => 112.768000,
            'status_verifikasi' => 'terkirim',
        ]);

        $service = new DuplicateCheckingService();

        // Test RULE-RED-01 (Exact KK Match)
        $resRule1 = $service->check('3515010101018888', -7.300000, 112.800000, '3515010101019999');
        $this->assertTrue($resRule1['is_duplicate']);
        $this->assertEquals('DUPLICATE_FAMILY_CARD_OR_NIK', $resRule1['redundancy_flag']);

        // Test RULE-RED-02 (Spatial Parcel Collision <= 15.0m)
        $resRule2 = $service->check('3515010101017777', -7.250005, 112.768005, '3515010101017777');
        $this->assertTrue($resRule2['is_duplicate']);
        $this->assertEquals('MULTI_FAMILY_SAME_PARCEL', $resRule2['redundancy_flag']);
        $this->assertCount(1, $resRule2['side_by_side_records']);
    }

    /** @test */
    public function redundancy_resolution_special_override_updates_flag_and_status()
    {
        Storage::fake('public');

        $verifikator = User::create([
            'name' => 'Verifikator ESDM Test',
            'email' => 'verif_test@esdm.go.id',
            'password' => bcrypt('password'),
            'role' => 'verifikator_esdm',
        ]);

        $warga = Warga::create([
            'nik' => '3515010101010005',
            'nama' => 'Penerima Override',
            'kabupaten' => 'Sidoarjo',
            'kecamatan' => 'Krian',
            'desa' => 'Sidomulyo',
            'rt_rw' => '02/01',
            'no_hp' => '081234567895',
            'alamat' => 'Rumah Petak Sekat A',
            'latitude' => -7.250000,
            'longitude' => 112.768000,
            'redundancy_flag' => 'MULTI_FAMILY_SAME_PARCEL',
            'status_verifikasi' => 'terkirim',
        ]);

        $fileSekat = UploadedFile::fake()->image('sekat_fisik.jpg');

        $response = $this->actingAs($verifikator)->patch(route('dinasesdm.redundancy.resolve', $warga->id), [
            'resolution_type' => 'special_override',
            'justification_note' => 'Terbukti 2 sekat bangunan fisik independen kontrakan petak.',
            'foto_sekat_fisik' => $fileSekat,
        ]);

        $response->assertRedirect(route('dinasesdm.datalist'));

        $warga->refresh();
        $this->assertEquals('OVERRIDDEN', $warga->redundancy_flag);
        $this->assertEquals('menunggu_verifikasi_pusat', $warga->status_verifikasi);
        $this->assertNotNull($warga->foto_sekat_fisik);
    }
}
