<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Level;
use App\Models\Role;
use App\Models\Wilayah;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Mockery;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }

    public function test_invalid_token_and_another_users_token_cannot_reset_password(): void
    {
        Notification::fake();
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $this->post('/forgot-password', ['email' => $owner->email])->assertSessionHasNoErrors();
        $token = Notification::sent($owner, ResetPassword::class)->sole()->token;

        foreach ([
            ['email' => $owner->email, 'token' => 'invalid-token'],
            ['email' => $other->email, 'token' => $token],
        ] as $credentials) {
            $this->post('/reset-password', $credentials + [
                'password' => 'NewPassword123', 'password_confirmation' => 'NewPassword123',
            ])->assertSessionHasErrors('email');
        }

        $this->post('/login', ['email' => $owner->email, 'password' => 'password'])
            ->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($owner);
    }

    public function test_expired_reset_token_cannot_change_password(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $this->post('/forgot-password', ['email' => $user->email])->assertSessionHasNoErrors();
        $token = Notification::sent($user, ResetPassword::class)->sole()->token;

        $this->travel(61)->minutes();
        try {
            $this->post('/reset-password', [
                'token' => $token, 'email' => $user->email,
                'password' => 'NewPassword123', 'password_confirmation' => 'NewPassword123',
            ])->assertSessionHasErrors('email');
        } finally {
            $this->travelBack();
        }

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($user);
    }

    public function test_confirmation_failure_preserves_token_and_successful_reset_is_single_use(): void
    {
        Notification::fake();
        $user = User::factory()->create(['status' => User::STATUS_PENDING]);
        $user->roles()->attach(Role::firstOrCreate(['name' => 'alumni']));
        $level = Level::create(['name' => 'Dasar', 'is_active' => true]);
        $user->update(['level_id' => $level->id]);
        $this->post('/forgot-password', ['email' => $user->email])->assertSessionHasNoErrors();
        $token = Notification::sent($user, ResetPassword::class)->sole()->token;

        $this->post('/reset-password', [
            'token' => $token, 'email' => $user->email,
            'password' => 'NewPassword123', 'password_confirmation' => 'Mismatch123',
        ])->assertSessionHasErrors('password');

        $this->post('/reset-password', [
            'token' => $token, 'email' => $user->email,
            'password' => 'NewPassword123', 'password_confirmation' => 'NewPassword123',
        ])->assertSessionHasNoErrors()->assertRedirect(route('login'));

        $this->post('/reset-password', [
            'token' => $token, 'email' => $user->email,
            'password' => 'ChangedAgain123', 'password_confirmation' => 'ChangedAgain123',
        ])->assertSessionHasErrors('email');
        $this->assertSame(User::STATUS_PENDING, $user->fresh()->status);
        $this->assertSame($level->id, $user->fresh()->level_id);
        $this->assertTrue($user->hasRole('alumni'));
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->post('/login', ['email' => $user->email, 'password' => 'NewPassword123'])
            ->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($user);
    }

    public function test_duplicate_reset_link_request_is_throttled(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email])->assertSessionHasNoErrors();
        $this->post('/forgot-password', ['email' => $user->email])->assertSessionHasErrors('email');
        Notification::assertSentToTimes($user, ResetPassword::class, 1);
    }

    public function test_imported_alumni_can_set_password_and_sign_in_without_changing_account_rights(): void
    {
        Notification::fake();
        $region = Wilayah::create(['name' => 'Jakarta']);
        $admin = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $admin->roles()->attach(Role::firstOrCreate(['name' => 'admin']));
        $csv = "name,email,wilayah,phone,angkatan,pekerjaan\n".
            "Nadia,nadia@example.test,Jakarta,08123456789,2020,Guru\n";

        $this->actingAs($admin)->post(route('alumni.csv.import'), [
            'file' => UploadedFile::fake()->createWithContent('alumni.csv', $csv),
        ])->assertOk()->assertSee('1 alumni berhasil diimpor');
        $alumni = User::where('email', 'nadia@example.test')->sole();
        $this->assertSame($region->id, $alumni->wilayah_id);
        $this->post('/logout')->assertRedirect();

        $this->post('/forgot-password', ['email' => $alumni->email])->assertSessionHasNoErrors();
        $token = Notification::sent($alumni, ResetPassword::class)->sole()->token;
        $this->post('/reset-password', [
            'token' => $token, 'email' => $alumni->email,
            'password' => 'NewPassword123', 'password_confirmation' => 'NewPassword123',
        ])->assertSessionHasNoErrors()->assertRedirect(route('login'));
        $this->post('/login', ['email' => $alumni->email, 'password' => 'NewPassword123'])
            ->assertSessionHasNoErrors();

        $this->assertAuthenticatedAs($alumni);
        $this->assertSame(User::STATUS_PENDING, $alumni->fresh()->status);
        $this->assertNull($alumni->level_id);
        $this->assertFalse($alumni->hasVerifiedEmail());
        $this->assertTrue($alumni->hasRole('alumni'));
        $this->assertFalse($alumni->isAdminLike());
    }

    public function test_forgot_password_reports_mail_transport_failure_as_email_error(): void
    {
        $user = User::factory()->create();
        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('send')->once()->andThrow(new TransportException('SMTP unavailable'));
        app()->instance(Dispatcher::class, $dispatcher);

        $this->post('/forgot-password', ['email' => $user->email])
            ->assertRedirect()->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
}
