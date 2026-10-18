<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_koorda_statistics_and_distributions_only_include_their_region(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 16));
        $local = Wilayah::create(['name' => 'Wilayah Lokal']);
        $other = Wilayah::create(['name' => 'Wilayah Lain']);
        $koorda = $this->member('koorda', $local->id);
        User::factory()->create(['wilayah_id' => $local->id, 'status' => 'pending', 'angkatan' => 2020]);
        User::factory()->create(['wilayah_id' => $other->id, 'status' => 'pending', 'angkatan' => 2021]);

        $this->actingAs($koorda)->get('/dashboard?wilayah_id='.$other->id)
            ->assertOk()
            ->assertViewHas('totalAlumni', 2)
            ->assertViewHas('alumniAktif', 1)
            ->assertViewHas('alumniBaru', 2)
            ->assertViewHas('pendingCount', 1)
            ->assertViewHas('persenVerifikasi', 50)
            ->assertViewHas('chartAlumni', fn ($chart) => $chart['series'][9] === 2 && array_sum($chart['series']) === 2)
            ->assertViewHas('sebaranAngkatan', fn ($rows) => $rows->pluck('angkatan')->all() === [2020])
            ->assertViewHas('sebaranWilayah', fn ($rows) => $rows->count() === 1 && $rows[0]->id === $local->id && (int) $rows[0]->total === 2)
            ->assertDontSee('Wilayah Lain');
    }

    public function test_admin_and_super_admin_can_see_all_regions_and_only_count_new_members_in_current_year(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 16));
        $local = Wilayah::create(['name' => 'Wilayah Lokal']);
        $other = Wilayah::create(['name' => 'Wilayah Lain']);
        $admin = $this->member('admin', $local->id);
        $superAdmin = $this->member('super_admin', null);
        User::factory()->create(['wilayah_id' => $other->id, 'status' => 'pending', 'created_at' => '2025-10-10']);
        User::factory()->create(['wilayah_id' => $other->id, 'status' => 'suspended', 'created_at' => '2026-09-10']);

        foreach ([$admin, $superAdmin] as $viewer) {
            $this->actingAs($viewer)->get('/dashboard')
                ->assertOk()
                ->assertViewHas('totalAlumni', 4)
                ->assertViewHas('alumniBaru', 2)
                ->assertViewHas('alumniAktif', 2)
                ->assertViewHas('pendingCount', 1)
                ->assertViewHas('chartAlumni', fn ($chart) => $chart['series'][8] === 1 && $chart['series'][9] === 2 && array_sum($chart['series']) === 3)
                ->assertViewHas('sebaranWilayah', fn ($rows) => $rows->count() === 2 && (int) $rows->sum('total') === 3)
                ->assertSee('Statistik kajian belum tersedia.');
        }
    }

    public function test_statistics_reject_koorda_without_a_region(): void
    {
        $viewer = $this->member('koorda', null);
        foreach ($this->statisticUrls() as $url) {
            $this->actingAs($viewer)->get($url)->assertForbidden();
        }
    }

    public function test_alumni_see_their_personal_dashboard_and_cannot_read_statistics(): void
    {
        $region = Wilayah::create(['name' => 'Wilayah Alumni']);
        foreach ([null, $region->id] as $wilayahId) {
            $viewer = $this->member('alumni', $wilayahId);
            $this->actingAs($viewer)->get('/dashboard')->assertOk()
                ->assertViewIs('dashboard.alumni')
                ->assertDontSee('Total Alumni')
                ->assertDontSee('Sebaran Alumni');
            foreach (array_slice($this->statisticUrls(), 1) as $url) {
                $this->get($url)->assertForbidden();
            }
        }
    }

    public function test_guest_cannot_access_chart_endpoints(): void
    {
        foreach (array_slice($this->statisticUrls(), 1) as $url) {
            $this->getJson($url)->assertUnauthorized();
        }
    }

    public function test_all_chart_endpoints_apply_region_scope_and_admin_override(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 16));
        $local = Wilayah::create(['name' => 'Wilayah Lokal']);
        $other = Wilayah::create(['name' => 'Wilayah Lain']);
        $koorda = $this->member('koorda', $local->id);
        $admin = $this->member('admin', $other->id);
        $koorda->update(['status_pernikahan' => 'Lajang', 'ab' => 'iya']);
        $admin->update(['status_pernikahan' => 'Menikah', 'ab' => 'tidak']);
        User::factory()->create(['wilayah_id' => $local->id, 'status' => 'pending', 'ab' => 'iya', 'status_pernikahan' => 'Lajang']);

        $this->actingAs($koorda)->getJson('/charts/alumni/status?wilayah_id='.$other->id)
            ->assertOk()->assertExactJson(['total' => 2, 'active' => 1, 'pending' => 1]);
        $this->getJson('/charts/alumni/monthly?year=2026')->assertOk()->assertJsonPath('values.9', 2);
        $this->getJson('/charts/alumni/status-pernikahan')->assertOk()->assertExactJson(['labels' => ['Lajang'], 'values' => [2]]);
        $this->getJson('/charts/alumni/ab')->assertOk()->assertExactJson(['labels' => ['iya'], 'values' => [2]]);

        $this->actingAs($admin)->getJson('/charts/alumni/status')
            ->assertOk()->assertExactJson(['total' => 3, 'active' => 2, 'pending' => 1]);
        $this->getJson('/charts/alumni/monthly?year=2026')->assertOk()->assertJsonPath('values.9', 3);
        foreach (['status-pernikahan', 'ab'] as $chart) {
            $this->getJson('/charts/alumni/'.$chart)->assertOk()
                ->assertJson(fn ($json) => $json->has('labels', 2)->where('values', fn ($values) => array_sum($values->all()) === 3));
        }

        $admin->roles()->sync([Role::firstOrCreate(['name' => 'super_admin'])->id]);
        $admin->update(['wilayah_id' => null]);
        $this->actingAs($admin)->getJson('/charts/alumni/status')
            ->assertOk()->assertExactJson(['total' => 3, 'active' => 2, 'pending' => 1]);
        $this->getJson('/charts/alumni/monthly?year=2026')->assertOk()->assertJsonPath('values.9', 3);
        foreach (['status-pernikahan', 'ab'] as $chart) {
            $this->getJson('/charts/alumni/'.$chart)->assertOk()
                ->assertJson(fn ($json) => $json->has('labels', 2)->where('values', fn ($values) => array_sum($values->all()) === 3));
        }
    }

    private function member(string $role, ?int $wilayahId): User
    {
        $user = User::factory()->create(['wilayah_id' => $wilayahId, 'status' => 'active']);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));

        return $user;
    }

    private function statisticUrls(): array
    {
        return ['/dashboard', '/charts/alumni/monthly', '/charts/alumni/status', '/charts/alumni/status-pernikahan', '/charts/alumni/ab'];
    }
}
