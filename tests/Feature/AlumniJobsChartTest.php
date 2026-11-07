<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlumniJobsChartTest extends TestCase
{
    use RefreshDatabase;

    public function test_jobs_are_normalized_without_merging_different_occupations(): void
    {
        $admin = $this->member('admin', null, 'Guru');
        foreach ([' guru ', "Guru  Sekolah", "guru\t sekolah", 'Pengajar', null, '   '] as $job) {
            User::factory()->create(['pekerjaan' => $job]);
        }

        $response = $this->actingAs($admin)->getJson(route('charts.alumni.jobs'))
            ->assertOk()
            ->assertExactJson([
                'labels' => ['Guru', 'Guru Sekolah', 'Pengajar', 'Belum diisi'],
                'values' => [2, 2, 1, 2],
            ]);
        $response->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_jobs_show_top_five_then_other_and_missing_with_stable_ties(): void
    {
        $admin = $this->member('admin', null, 'A');
        foreach (['A', 'A', 'A', 'B', 'B', 'B', 'C', 'C', 'D', 'D', 'E', 'F', 'G', null, ' '] as $job) {
            User::factory()->create(['pekerjaan' => $job]);
        }

        $this->actingAs($admin)->getJson(route('charts.alumni.jobs'))
            ->assertExactJson([
                'labels' => ['A', 'B', 'C', 'D', 'E', 'Lainnya', 'Belum diisi'],
                'values' => [4, 3, 2, 2, 1, 2, 2],
            ]);
    }

    public function test_free_text_jobs_do_not_collide_with_summary_bucket_labels(): void
    {
        $admin = $this->member('admin', null, 'Lainnya');
        foreach (['Lainnya', 'Belum diisi', 'Belum diisi', 'Lainnya (gabungan)', 'Lainnya (gabungan)', 'Belum diisi (profil kosong)', 'Belum diisi (profil kosong)', 'A', 'B', 'C', null] as $job) {
            User::factory()->create(['pekerjaan' => $job]);
        }
        $this->actingAs($admin)->getJson(route('charts.alumni.jobs'))
            ->assertExactJson([
                'labels' => ['Belum diisi', 'Belum diisi (profil kosong)', 'Lainnya', 'Lainnya (gabungan)', 'A', 'Lainnya (gabungan 2)', 'Belum diisi (profil kosong 2)'],
                'values' => [2, 2, 2, 2, 1, 2, 1],
            ]);
    }

    public function test_region_scope_cannot_be_widened_and_global_roles_see_all_accounts(): void
    {
        $local = Wilayah::create(['name' => 'Lokal']);
        $other = Wilayah::create(['name' => 'Lain']);
        $koorda = $this->member('koorda', $local->id, 'Guru');
        $admin = $this->member('admin', $other->id, 'Pengajar');
        User::factory()->create(['wilayah_id' => $local->id, 'status' => 'pending', 'pekerjaan' => 'Guru']);
        User::factory()->create(['wilayah_id' => $other->id, 'status' => 'suspended', 'pekerjaan' => 'Pengajar']);

        $this->actingAs($koorda)->getJson(route('charts.alumni.jobs', ['wilayah_id' => $other->id]))
            ->assertExactJson(['labels' => ['Guru'], 'values' => [2]]);

        foreach ([$admin, $this->member('super_admin', null, 'Pengajar')] as $viewer) {
            $this->actingAs($viewer)->getJson(route('charts.alumni.jobs'))
                ->assertExactJson(['labels' => ['Pengajar', 'Guru'], 'values' => [3, 2]]);
        }
    }

    public function test_guests_and_ineligible_accounts_cannot_read_jobs(): void
    {
        $this->getJson(route('charts.alumni.jobs'))->assertUnauthorized();
        $region = Wilayah::create(['name' => 'Lokal']);
        foreach ([['alumni', 'active'], ['koorda', 'pending'], ['admin', 'suspended']] as [$role, $status]) {
            $viewer = $this->member($role, $region->id, null, $status);
            $this->actingAs($viewer)->getJson(route('charts.alumni.jobs'))->assertForbidden();
        }
    }

    public function test_monthly_chart_stops_at_current_month_for_current_year(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 16));
        $admin = $this->member('admin', null, null);
        User::factory()->create(['created_at' => '2026-12-01']);

        $this->actingAs($admin)->getJson(route('charts.alumni.monthly', ['year' => 2026]))
            ->assertOk()
            ->assertJsonCount(10, 'labels')
            ->assertJsonCount(10, 'values')
            ->assertJsonPath('values.9', 1);
        $this->getJson(route('charts.alumni.monthly', ['year' => 2025]))
            ->assertOk()
            ->assertJsonCount(12, 'labels')
            ->assertJsonCount(12, 'values');
    }

    private function member(string $role, ?int $regionId, ?string $job, string $status = 'active'): User
    {
        $user = User::factory()->create([
            'wilayah_id' => $regionId,
            'pekerjaan' => $job,
            'status' => $status,
        ]);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));

        return $user;
    }
}
