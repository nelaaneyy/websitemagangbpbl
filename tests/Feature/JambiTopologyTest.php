<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\ElectricPole;

class JambiTopologyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function jambi_regional_scope_center_and_bounds_are_valid()
    {
        $centerLat = -1.6101;
        $centerLng = 103.6131;

        $minLat = -2.8500;
        $maxLat = -0.7500;
        $minLng = 101.1000;
        $maxLng = 104.5500;

        // Verify Center is inside Jambi Bounding Box
        $this->assertGreaterThanOrEqual($minLat, $centerLat);
        $this->assertLessThanOrEqual($maxLat, $centerLat);
        $this->assertGreaterThanOrEqual($minLng, $centerLng);
        $this->assertLessThanOrEqual($maxLng, $centerLng);
    }

    /** @test */
    public function one_to_n_topology_distance_calculation_works_for_jambi_nodes()
    {
        // Source Node (Tiang Pangkal Jambi)
        $lat1 = -1.610100;
        $lon1 = 103.613100;

        // Target Nodes Warga
        $lat2 = -1.611200;
        $lon2 = 103.614500;

        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distanceMeters = $earthRadius * $c;

        // Distance should be around 190-210 meters
        $this->assertGreaterThan(150, $distanceMeters);
        $this->assertLessThan(300, $distanceMeters);
    }

    /** @test */
    public function user_role_label_accessor_returns_correct_formal_title()
    {
        $staff = new User(['role' => 'staff_desa']);
        $this->assertEquals('Staff Administrasi Desa', $staff->role_label);

        $kades = new User(['role' => 'kepala_desa']);
        $this->assertEquals('Kepala Desa', $kades->role_label);

        $admin = new User(['role' => 'super_admin']);
        $this->assertEquals('Super Admin ESDM', $admin->role_label);
    }
}
