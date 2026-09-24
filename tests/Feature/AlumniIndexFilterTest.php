<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Level;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlumniIndexFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_alumni_index_shows_filter_summary_and_reset_when_filter_is_active(): void
    {
        $admin = $this->adminUser();
        User::factory()->create([
            'name' => 'Fatimah Alumni',
            'email' => 'fatimah@example.test',
            'status' => User::STATUS_PENDING,
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('alumni.index', ['q' => 'Fatimah']));

        $response
            ->assertOk()
            ->assertSee('Filter aktif')
            ->assertSee('Cari: Fatimah')
            ->assertSee('Reset')
            ->assertSee('Fatimah Alumni');
    }

    public function test_alumni_index_explains_empty_results_when_filters_match_no_alumni(): void
    {
        $admin = $this->adminUser();
        User::factory()->create([
            'name' => 'Zahra Alumni',
            'email' => 'zahra@example.test',
            'status' => User::STATUS_PENDING,
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('alumni.index', ['q' => 'TidakAdaYangCocok']));

        $response
            ->assertOk()
            ->assertSee('Tidak ada alumni yang cocok dengan filter.')
            ->assertSee('Reset Filter')
            ->assertDontSee('Belum ada data alumni.');
    }

    public function test_koorda_role_badge_uses_distinct_color_from_alumni(): void
    {
        $admin = $this->adminUser();
        $koordaRole = Role::firstOrCreate(['name' => 'koorda']);

        $koorda = User::factory()->create([
            'name' => 'Diah Eka Pratika',
            'status' => User::STATUS_ACTIVE,
        ]);
        $koorda->roles()->attach($koordaRole->id);

        $response = $this
            ->actingAs($admin)
            ->get(route('alumni.index'));

        $response
            ->assertOk()
            ->assertSee('Koorda')
            ->assertSee('bg-violet-200 text-violet-800', false)
            ->assertDontSee('bg-emerald-200 text-emerald-800', false);
    }

    public function test_alumni_index_shows_learning_level_column_instead_of_status_column(): void
    {
        $admin = $this->adminUser();
        $level = Level::create([
            'name' => 'Level 4',
            'description' => 'akhir',
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'Alumni Level Akhir',
            'level_id' => $level->id,
            'status' => User::STATUS_ACTIVE,
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('alumni.index'));

        $response
            ->assertOk()
            ->assertSee('<th class="px-4 py-3 text-left">Level Materi</th>', false)
            ->assertSee('akhir')
            ->assertDontSee('<th class="px-4 py-3 text-left">Status</th>', false);
    }

    public function test_action_buttons_are_icon_only_with_accessible_labels(): void
    {
        $admin = $this->adminUser();
        $alumniRole = Role::firstOrCreate(['name' => 'alumni']);
        $alumni = User::factory()->create([
            'name' => 'Alumni Icon Only',
            'status' => User::STATUS_ACTIVE,
        ]);
        $alumni->roles()->attach($alumniRole->id);

        $response = $this
            ->actingAs($admin)
            ->get(route('alumni.index'));

        $response
            ->assertOk()
            ->assertSee('aria-label="Detail Alumni Icon Only"', false)
            ->assertSee('aria-label="Edit Alumni Icon Only"', false)
            ->assertSee('aria-label="Hapus Alumni Icon Only"', false)
            ->assertSee('fas fa-eye', false)
            ->assertSee('fas fa-pen', false)
            ->assertSee('fas fa-trash', false)
            ->assertDontSee('<i class="fas fa-eye"></i> Detail', false)
            ->assertDontSee('<i class="fas fa-pen"></i> Edit', false)
            ->assertDontSee('<i class="fas fa-trash"></i> Hapus', false);
    }

    private function adminUser(): User
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create([
            'email' => 'admin-filter@example.test',
            'status' => User::STATUS_ACTIVE,
        ]);

        $admin->roles()->attach($adminRole->id);

        return $admin;
    }
}
