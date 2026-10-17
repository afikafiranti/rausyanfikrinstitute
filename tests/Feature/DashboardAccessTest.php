<?php

namespace Tests\Feature;

use App\Models\{Role, User, Wilayah};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_managers_cannot_read_dashboard_statistics(): void
    {
        $region = Wilayah::create(['name' => 'Wilayah Uji']);
        foreach (['admin', 'super_admin', 'koorda'] as $role) {
            foreach (['pending', 'suspended'] as $status) {
                $viewer = User::factory()->create(['status' => $status, 'wilayah_id' => $region->id]);
                $viewer->roles()->attach(Role::firstOrCreate(['name' => $role]));
                $this->actingAs($viewer)->get('/dashboard')->assertForbidden();
            }
        }
    }

    public function test_guest_is_redirected_to_login_before_accessing_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_unauthenticated_json_request_cannot_access_dashboard(): void
    {
        $this->getJson('/dashboard')->assertUnauthorized();
    }
}
