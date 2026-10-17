<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use App\Models\Level;
use App\Models\Playlist;
use App\Models\Wilayah;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Mockery;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        Notification::fake();
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'status' => User::STATUS_ACTIVE,
            'level_id' => 4,
            'roles' => ['super_admin'],
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('profile.edit', absolute: false));
        $user = User::where('email', 'test@example.com')->firstOrFail();
        $this->assertSame(User::STATUS_PENDING, $user->status);
        $this->assertNull($user->level_id);
        $this->assertTrue($user->hasRole('alumni'));
        $this->assertFalse($user->hasRole('super_admin'));
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_duplicate_invalid_email_and_password_mismatch_do_not_create_accounts(): void
    {
        Notification::fake();
        User::factory()->create(['email' => 'existing@example.test']);

        foreach ([
            ['email' => 'existing@example.test', 'error' => 'email'],
            ['email' => 'not-an-email', 'error' => 'email'],
            ['email' => 'new@example.test', 'error' => 'password'],
        ] as $case) {
            $this->post('/register', [
                'name' => 'Akun Baru',
                'email' => $case['email'],
                'password' => 'Password123',
                'password_confirmation' => $case['error'] === 'password' ? 'Mismatch123' : 'Password123',
                'roles' => ['super_admin'],
                'status' => User::STATUS_ACTIVE,
            ])->assertSessionHasErrors($case['error']);
        }

        $this->assertSame(1, User::count());
        $this->assertGuest();
        Notification::assertNothingSent();
    }

    public function test_registration_survives_verification_mail_transport_failure(): void
    {
        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('send')->once()->andThrow(new TransportException('SMTP unavailable'));
        app()->instance(Dispatcher::class, $dispatcher);

        $this->post('/register', [
            'name' => 'Alumni Baru', 'email' => 'mail-failed@example.test',
            'password' => 'Password123', 'password_confirmation' => 'Password123',
        ])->assertRedirect(route('verification.notice'))
            ->assertSessionHas('status', 'verification-link-failed');

        $user = User::where('email', 'mail-failed@example.test')->sole();
        $this->assertAuthenticatedAs($user);
        $this->assertSame(User::STATUS_PENDING, $user->status);
        $this->assertTrue($user->hasRole('alumni'));
        $this->assertFalse($user->hasVerifiedEmail());
    }

    public function test_authenticated_admin_cannot_register_another_account_or_lose_their_session(): void
    {
        $admin = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $admin->roles()->attach(Role::firstOrCreate(['name' => 'admin']));

        $this->actingAs($admin)->post('/register', [
            'name' => 'Akun Lain', 'email' => 'other@example.test',
            'password' => 'Password123', 'password_confirmation' => 'Password123',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($admin);
        $this->assertDatabaseMissing('users', ['email' => 'other@example.test']);
    }

    public function test_registered_alumni_can_complete_profile_and_gain_material_access_after_regional_approval(): void
    {
        Notification::fake();
        $region = Wilayah::create(['name' => 'Wilayah Alumni']);
        $level = Level::create(['name' => 'Level 1', 'description' => 'dasar', 'is_active' => true]);
        Playlist::create(['title' => 'Materi Pembuka', 'youtube_playlist_id' => 'PLSTART', 'level_id' => $level->id, 'is_active' => true]);
        $koorda = User::factory()->create(['status' => User::STATUS_ACTIVE, 'wilayah_id' => $region->id]);
        $koorda->roles()->attach(Role::firstOrCreate(['name' => 'koorda']));

        $this->post('/register', [
            'name' => 'Alumni Baru', 'email' => 'new@example.test',
            'password' => 'Password123', 'password_confirmation' => 'Password123',
        ])->assertSessionHasNoErrors()->assertRedirect(route('profile.edit'));
        $alumni = User::where('email', 'new@example.test')->firstOrFail();
        $this->get(route('materi.index'))->assertForbidden();
        $this->patch(route('profile.update'), [
            'name' => $alumni->name, 'email' => $alumni->email, 'wilayah_id' => $region->id,
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->post('/logout')->assertRedirect();
        $this->post('/login', ['email' => $koorda->email, 'password' => 'password'])->assertSessionHasNoErrors()->assertRedirect();
        $this->get(route('verification.index'))->assertOk()->assertSee('Alumni Baru');
        $this->post(route('verification.approve', $alumni), ['level_id' => $level->id])
            ->assertSessionHasNoErrors()->assertRedirect();

        $this->post('/logout')->assertRedirect();
        $this->post('/login', ['email' => $alumni->email, 'password' => 'Password123'])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertAuthenticatedAs($alumni);
        $this->get(route('materi.index'))->assertOk()->assertSee('Materi Pembuka');
    }
}
