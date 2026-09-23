<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VillageOfficerAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_input_or_cek_page()
    {
        $responseInput = $this->get('/input');
        $responseInput->assertRedirect('/login');

        $responseCek = $this->get('/cek');
        $responseCek->assertRedirect('/login');
    }

    public function test_staff_desa_can_access_input_and_cek_page()
    {
        $staff = User::factory()->create([
            'role' => 'staff_desa',
            'desa' => 'BAGAN PETE',
        ]);

        $responseInput = $this->actingAs($staff)->get('/input');
        $responseInput->assertStatus(200);

        $responseCek = $this->actingAs($staff)->get('/cek');
        $responseCek->assertStatus(200);
    }

    public function test_kepala_desa_can_access_input_and_cek_page()
    {
        $kades = User::factory()->create([
            'role' => 'kepala_desa',
            'desa' => 'KENALI BESAR',
        ]);

        $responseInput = $this->actingAs($kades)->get('/input');
        $responseInput->assertStatus(200);

        $responseCek = $this->actingAs($kades)->get('/cek');
        $responseCek->assertStatus(200);
    }
}
