<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_for_report_pages(): void
    {
        foreach (['laporan.index', 'laporan.review'] as $route) {
            $this->get(route($route))->assertRedirect(route('login'));
        }
    }

    public function test_authorized_reviewers_see_empty_queue_with_effective_region(): void
    {
        $local = Wilayah::create(['name' => 'Wilayah Lokal']);
        $other = Wilayah::create(['name' => 'Wilayah Lain']);
        foreach (['admin', 'super_admin', 'koorda'] as $role) {
            $viewer = $this->member($role, $role === 'koorda' ? $local->id : null);
            $this->actingAs($viewer)->get(route('laporan.review', ['wilayah_id' => $other->id]))
                ->assertOk()->assertSee('Tidak ada laporan yang menunggu review.')
                ->assertViewHas('wilayahId', $role === 'koorda' ? $local->id : $other->id)
                ->assertDontSee('Setujui')->assertDontSee('Tolak Laporan')->assertDontSee('Export');
        }
    }

    public function test_review_rejects_alumni_and_koorda_without_region(): void
    {
        $local = Wilayah::create(['name' => 'Wilayah Lokal']);
        foreach ([$this->member('alumni', $local->id), $this->member('koorda')] as $viewer) {
            $this->actingAs($viewer)->get(route('laporan.review'))->assertForbidden();
        }
    }

    public function test_unavailable_review_action_cannot_create_a_report(): void
    {
        $this->actingAs($this->member('alumni'));
        foreach (['laporan.review'] as $route) {
            $this->post(route($route), ['judul' => 'Tidak Boleh Tersimpan', 'tanggal' => '2026-10-16'])->assertStatus(405);
        }
        $this->assertDatabaseCount('kajian_reports', 0);
    }

    private function member(string $role, ?int $wilayahId = null): User
    {
        $user = User::factory()->create(['status' => 'active', 'wilayah_id' => $wilayahId]);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));

        return $user;
    }
}
