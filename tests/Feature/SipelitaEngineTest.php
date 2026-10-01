<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Warga;
use App\Models\ElectricPole;
use App\Models\WorkOrder;
use App\Models\LisdesCluster;
use App\Services\SpatialEngine;
use App\Services\DuplicateCheckingService;
use App\Services\ExifValidationService;
use App\Services\KmlExporterService;
use App\Services\SpatialClusteringService;
use Illuminate\Http\UploadedFile;

class SipelitaEngineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed tiang listrik
        ElectricPole::create([
            'kode_tiang' => 'TR-TEST-01',
            'jenis' => 'TR',
            'latitude' => -7.250000,
            'longitude' => 112.768000,
            'status' => 'aktif',
        ]);
    }

    /** @test */
    public function haversine_distance_calculation_is_accurate()
    {
        // Jarak antara 2 titik diketahui
        $lat1 = -7.250000;
        $lng1 = 112.768000;

        $lat2 = -7.251000;
        $lng2 = 112.768000;

        $distance = SpatialEngine::haversineDistance($lat1, $lng1, $lat2, $lng2);

        // Jarak ~111 meter
        $this->assertGreaterThan(100, $distance);
        $this->assertLessThan(125, $distance);
    }

    /** @test */
    public function nearest_pole_search_returns_valid_pole_and_buffer()
    {
        $result = SpatialEngine::findNearestPole(-7.250050, 112.768050);

        $this->assertNotNull($result);
        $this->assertEquals('TR-TEST-01', $result['pole']->kode_tiang);
        $this->assertTrue($result['qualifies_bpbl']); // Jarak sangat dekat < 200m
        $this->assertLessThan(20, $result['distance_meters']);
    }

    /** @test */
    public function three_layer_duplicate_checking_detects_spatial_and_nik_duplicates()
    {
        // Insert 1 warga existing
        $warga1 = Warga::create([
            'nik' => '3515010101010001',
            'nama' => 'Budi Santoso',
            'kabupaten' => 'Sidoarjo',
            'kecamatan' => 'Krian',
            'desa' => 'Sidomulyo',
            'rt_rw' => '',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Merdeka No. 1',
            'latitude' => -7.250000,
            'longitude' => 112.768000,
            'status_verifikasi' => 'terkirim',
        ]);

        $service = new DuplicateCheckingService();

        // Check 1: NIK sama
        $resNik = $service->check('3515010101010001', -7.300000, 112.800000);
        $this->assertTrue($resNik['is_duplicate']);
        $this->assertEquals('tinggi', $resNik['risk_level']);

        // Check 2: Proksimitas Spasial 15m di titik yang sama
        $resSpatial = $service->check('3515010101019999', -7.250005, 112.768005);
        $this->assertTrue($resSpatial['is_duplicate']);
        $this->assertEquals('MULTI_FAMILY_SAME_PARCEL', $resSpatial['redundancy_flag']);

        // Check 3: NIK & Lokasi baru (bebas duplikasi)
        $resClean = $service->check('3515010101018888', -7.350000, 112.850000);
        $this->assertFalse($resClean['is_duplicate']);
        $this->assertEquals('rendah', $resClean['risk_level']);
    }

    /** @test */
    public function kml_exporter_generates_valid_xml()
    {
        Warga::create([
            'nik' => '3515010101010002',
            'nama' => 'Siti Aminah',
            'kabupaten' => 'Sidoarjo',
            'kecamatan' => 'Krian',
            'desa' => 'Sidomulyo',
            'rt_rw' => '',
            'no_hp' => '081234567891',
            'alamat' => 'Jl. Mawar No. 5',
            'latitude' => -7.251000,
            'longitude' => 112.769000,
            'status_verifikasi' => 'lolos_verifikasi_pusat',
        ]);

        $kmlService = new KmlExporterService();
        $xml = $kmlService->generateKml();

        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $xml);
        $this->assertStringContainsString('<kml xmlns="http://www.opengis.net/kml/2.2">', $xml);
        $this->assertStringContainsString('Penerima Manfaat BPBL', $xml);
        $this->assertStringContainsString('3515************', $xml); // Masked NIK in KML
    }

    /** @test */
    public function spatial_clustering_groups_unserved_nodes_into_cluster()
    {
        // 2 Warga >200m dari tiang terdekat dan berdekatan satu sama lain
        Warga::create([
            'nik' => '3515010101010010',
            'nama' => 'Warga Pelosok 1',
            'kabupaten' => 'Sidoarjo',
            'kecamatan' => 'Krian',
            'desa' => 'Sidomulyo',
            'rt_rw' => '',
            'no_hp' => '081234567892',
            'alamat' => 'Dusun Pelosok',
            'latitude' => -7.280000,
            'longitude' => 112.800000,
            'status_verifikasi' => 'terkirim',
        ]);

        Warga::create([
            'nik' => '3515010101010011',
            'nama' => 'Warga Pelosok 2',
            'kabupaten' => 'Sidoarjo',
            'kecamatan' => 'Krian',
            'desa' => 'Sidomulyo',
            'rt_rw' => '',
            'no_hp' => '081234567893',
            'alamat' => 'Dusun Pelosok',
            'latitude' => -7.280100,
            'longitude' => 112.800100,
            'status_verifikasi' => 'terkirim',
        ]);

        $clusterService = new SpatialClusteringService();
        $result = $clusterService->generateLisdesClusters(300.0, 2);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['clusters_created']);
        $this->assertDatabaseHas('lisdes_clusters', [
            'desa' => 'Sidomulyo',
            'jumlah_usulan' => 2,
        ]);
    }
}
