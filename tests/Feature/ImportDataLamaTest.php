<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Http\UploadedFile;

class ImportDataLamaTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_data_lama_parses_realisasi_checkmark_correctly()
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'email' => 'admin@esdm.jambiprov.go.id',
        ]);

        $csvContent = "\xEF\xBB\xBF" . "No,KECAMATAN,DESA/KELURAHAN,NAMA,NIK,ALAMAT,Usulan,Realisasi,Keterangan\n" .
            "1,ALAM BARAJO,BAGAN PETE,SITI JAMILAH,1571074107470324,JL. LINGKAR BARAT II,2023,✓,Sudah terpasang\n" .
            "2,ALAM BARAJO,BAGAN PETE,SYAHRUL,1404112709940003,JL. SUNAN PANDARAN,2023,,Tanpa centang\n";

        $file = UploadedFile::fake()->createWithContent('import_test.csv', $csvContent);

        $response = $this->actingAs($superAdmin)
            ->post(route('dinasesdm.import.excel'), [
                'file' => $file,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify Row 1 (With Checkmark)
        $wargaChecked = Warga::where('nik', '1571074107470324')->first();
        $this->assertNotNull($wargaChecked);
        $this->assertEquals('SITI JAMILAH', $wargaChecked->nama);
        $this->assertEquals('terpasang', $wargaChecked->status_verifikasi);
        $this->assertFalse((bool)$wargaChecked->butuh_validasi_realisasi);

        // Verify Row 2 (Without Checkmark)
        $wargaUnchecked = Warga::where('nik', '1404112709940003')->first();
        $this->assertNotNull($wargaUnchecked);
        $this->assertEquals('SYAHRUL', $wargaUnchecked->nama);
        $this->assertEquals('menunggu_verifikasi_pusat', $wargaUnchecked->status_verifikasi);
        $this->assertTrue((bool)$wargaUnchecked->butuh_validasi_realisasi);
    }

    public function test_superadmin_can_access_and_confirm_realisasi_validation()
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $warga = Warga::create([
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
            'butuh_validasi_realisasi' => true,
            'tahun_usulan'             => '2024',
        ]);

        // Access validation list page
        $response = $this->actingAs($superAdmin)->get(route('dinasesdm.validasi_realisasi.index'));
        $response->assertStatus(200);
        $response->assertSee('SANDI PRASETYO');
        $response->assertSee('1571070904930001');

        // Confirm Single: Sudah Realisasi
        $confirmResponse = $this->actingAs($superAdmin)
            ->patch(route('dinasesdm.validasi_realisasi.confirm', $warga->id), [
                'keputusan' => 'sudah_realisasi',
            ]);

        $confirmResponse->assertRedirect();
        
        $warga->refresh();
        $this->assertEquals('terpasang', $warga->status_verifikasi);
        $this->assertFalse((bool)$warga->butuh_validasi_realisasi);
    }

    public function test_superadmin_can_bulk_confirm_realisasi_validation()
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $w1 = Warga::create([
            'nik'                      => '1571070000000001',
            'nama'                     => 'WARGA 1',
            'kabupaten'                => 'KOTA JAMBI',
            'kecamatan'                => 'ALAM BARAJO',
            'desa'                     => 'BAGAN PETE',
            'rt_rw'                    => '01/01',
            'no_hp'                    => '-',
            'alamat'                   => 'ALAMAT 1',
            'latitude'                 => 0.0,
            'longitude'                => 0.0,
            'status_verifikasi'        => 'menunggu_verifikasi_pusat',
            'butuh_validasi_realisasi' => true,
        ]);

        $w2 = Warga::create([
            'nik'                      => '1571070000000002',
            'nama'                     => 'WARGA 2',
            'kabupaten'                => 'KOTA JAMBI',
            'kecamatan'                => 'ALAM BARAJO',
            'desa'                     => 'BAGAN PETE',
            'rt_rw'                    => '01/01',
            'no_hp'                    => '-',
            'alamat'                   => 'ALAMAT 2',
            'latitude'                 => 0.0,
            'longitude'                => 0.0,
            'status_verifikasi'        => 'menunggu_verifikasi_pusat',
            'butuh_validasi_realisasi' => true,
        ]);

        $bulkResponse = $this->actingAs($superAdmin)
            ->post(route('dinasesdm.validasi_realisasi.bulk_confirm'), [
                'warga_ids' => [$w1->id, $w2->id],
                'keputusan' => 'sudah_realisasi',
            ]);

        $bulkResponse->assertRedirect();

        $this->assertEquals('terpasang', $w1->fresh()->status_verifikasi);
        $this->assertFalse((bool)$w1->fresh()->butuh_validasi_realisasi);

        $this->assertEquals('terpasang', $w2->fresh()->status_verifikasi);
        $this->assertFalse((bool)$w2->fresh()->butuh_validasi_realisasi);
    }
}
