<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use App\Notifications\AccountStatusNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlumniDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_alumni_sees_only_their_own_dashboard_and_unread_count(): void
    {
        $wilayah = Wilayah::create(['name' => 'Jakarta Selatan']);
        $level = Level::create(['name' => 'L1', 'description' => 'Level Dasar', 'is_active' => true]);
        $alumni = $this->alumni([
            'name' => '<script>alert(1)</script>',
            'status' => User::STATUS_ACTIVE,
            'level_id' => $level->id,
            'wilayah_id' => $wilayah->id,
            'phone' => '08123456789',
            'angkatan' => 2021,
        ]);
        $other = $this->alumni(['name' => 'Alumni Rahasia', 'status' => User::STATUS_ACTIVE]);
        $alumni->notify(new AccountStatusNotification('approved', ['level_name' => 'Level Dasar']));
        $other->notify(new AccountStatusNotification('pending'));

        $this->actingAs($alumni)->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('dashboard.alumni')
            ->assertViewHas('unreadCount', 1)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('Level Dasar')
            ->assertSee('Jakarta Selatan')
            ->assertSee('Data profil dasar lengkap')
            ->assertSee('Mulai belajar')
            ->assertSee(route('profile.edit'), false)
            ->assertSee(route('notifications.index'), false)
            ->assertDontSee('Alumni Rahasia')
            ->assertDontSee('Laporan')
            ->assertDontSee('Total Alumni')
            ->assertDontSee('Verifikasi pending');
    }

    public function test_pending_alumni_sees_profile_guidance_without_materi_link(): void
    {
        $level = Level::create(['name' => 'Level Dasar', 'is_active' => true]);
        $alumni = $this->alumni(['status' => User::STATUS_PENDING, 'level_id' => $level->id]);

        $this->actingAs($alumni)->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('dashboard.alumni')
            ->assertSee('Menunggu verifikasi')
            ->assertSee('Lengkapi data profil dasar')
            ->assertSee(route('profile.edit'), false)
            ->assertDontSee('Mulai belajar');
    }

    public function test_suspended_alumni_cannot_follow_materi_link(): void
    {
        $level = Level::create(['name' => 'Level Dasar', 'is_active' => true]);
        $alumni = $this->alumni(['status' => User::STATUS_SUSPENDED, 'level_id' => $level->id]);

        $this->actingAs($alumni)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Akun ditangguhkan')
            ->assertDontSee('Mulai belajar');
    }

    public function test_active_alumni_without_level_sees_why_materi_is_unavailable(): void
    {
        $alumni = $this->alumni(['status' => User::STATUS_ACTIVE, 'level_id' => null]);

        $this->actingAs($alumni)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Level materi belum ditetapkan')
            ->assertDontSee('Mulai belajar');
    }

    public function test_guest_must_log_in_before_opening_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_alumni_login_opens_their_dashboard(): void
    {
        $alumni = $this->alumni(['name' => 'Alumni Baru', 'status' => User::STATUS_PENDING]);

        $this->post(route('login'), ['email' => $alumni->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('dashboard.alumni')
            ->assertSee('Alumni Baru');
    }

    public function test_account_without_role_keeps_existing_profile_redirect(): void
    {
        $user = User::factory()->create(['status' => User::STATUS_ACTIVE]);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertRedirect(route('profile.edit'));
    }

    public function test_alumni_role_does_not_override_admin_dashboard_or_status_gate(): void
    {
        $admin = $this->alumni(['status' => User::STATUS_ACTIVE]);
        $admin->roles()->attach(Role::firstOrCreate(['name' => 'admin']));

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('dashboard.index');

        $admin->update(['status' => User::STATUS_PENDING]);
        $this->get(route('dashboard'))->assertForbidden();
    }

    private function alumni(array $attributes): User
    {
        $user = User::factory()->create($attributes);
        $user->roles()->attach(Role::firstOrCreate(['name' => 'alumni']));

        return $user;
    }
}
