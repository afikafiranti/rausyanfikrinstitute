<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AlumniListingQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_query_count_does_not_grow_with_displayed_rows(): void
    {
        $admin = $this->account('admin', 'Admin Actor');
        for ($number = 1; $number <= 15; $number++) {
            $this->account('alumni', sprintf('Alumni %02d', $number));
        }

        $roleQueries = 0;
        DB::listen(function ($query) use (&$roleQueries) {
            if (str_contains(strtolower($query->sql), 'role_user')) {
                $roleQueries++;
            }
        });

        $this->actingAs($admin)->get(route('alumni.index', ['per_page' => 5]))
            ->assertOk()->assertViewHas('alumni', fn ($rows) => $rows->count() === 5);
        $fiveRows = $roleQueries;

        $roleQueries = 0;
        $this->get(route('alumni.index', ['per_page' => 15]))
            ->assertOk()->assertViewHas('alumni', fn ($rows) => $rows->count() === 15);
        $fifteenRows = $roleQueries;

        $this->assertLessThanOrEqual(2, $fifteenRows - $fiveRows,
            "Role queries grew from {$fiveRows} to {$fifteenRows} when ten rows were added.");
    }

    public function test_display_and_endpoint_follow_current_multi_role_assignments(): void
    {
        $admin = $this->account('admin', 'Admin Actor');
        $target = $this->account('alumni', 'Alumni Target');
        $action = 'action="'.route('alumni.destroy', $target).'"';

        $this->actingAs($admin)->get(route('alumni.index'))
            ->assertOk()->assertSee($action, false);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $target->roles()->attach($adminRole);
        $this->get(route('alumni.index'))
            ->assertOk()->assertDontSee($action, false);
        $this->delete(route('alumni.destroy', $target))->assertSessionHasErrors('delete');

        $target->roles()->detach($adminRole);
        $this->get(route('alumni.index'))
            ->assertOk()->assertSee($action, false);

        $admin->update(['status' => User::STATUS_SUSPENDED]);
        $this->get(route('alumni.index'))->assertForbidden();
    }

    private function account(string $roleName, string $name): User
    {
        $user = User::factory()->create(['name' => $name, 'status' => User::STATUS_ACTIVE]);
        $user->roles()->attach(Role::firstOrCreate(['name' => $roleName]));

        return $user;
    }
}
