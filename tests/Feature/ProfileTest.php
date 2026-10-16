<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_profile_photo_can_be_uploaded_and_read_as_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'photo' => UploadedFile::fake()->image('avatar.jpg'),
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertNotNull($user->photo_url);
        $this->assertStringStartsWith('/storage/avatars/', $user->photo_url);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $user->photo_url));
        $this->assertSame($user->photo_url, $user->avatar_url);
        $this->assertTrue($user->has_avatar);
    }

    public function test_invalid_personal_fields_do_not_change_profile(): void
    {
        $user = User::factory()->create(['status' => User::STATUS_ACTIVE, 'angkatan' => 2020]);

        $this->actingAs($user)->patch('/profile', [
            'name' => 'Changed',
            'email' => $user->email,
            'angkatan' => 70000,
            'tanggal_lahir' => 'bukan-tanggal',
            'status_pernikahan' => 'invalid',
            'ab' => 'invalid',
        ])->assertSessionHasErrors(['angkatan', 'tanggal_lahir', 'status_pernikahan', 'ab']);

        $this->assertSame($user->name, $user->fresh()->name);
        $this->assertSame(2020, $user->fresh()->angkatan);
    }

    public function test_valid_personal_fields_are_saved_without_clearing_omitted_optional_fields(): void
    {
        $user = User::factory()->create(['phone' => '08123456', 'status' => User::STATUS_ACTIVE]);

        $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'tanggal_lahir' => '1999-05-12',
            'status_pernikahan' => 'menikah',
            'ab' => 'iya',
        ])->assertSessionHasNoErrors();

        $this->assertSame('08123456', $user->fresh()->phone);
        $this->assertSame('1999-05-12', $user->fresh()->tanggal_lahir);
        $this->assertSame('menikah', $user->fresh()->status_pernikahan);
        $this->assertSame('iya', $user->fresh()->ab);
    }

    public function test_profile_accepts_schema_length_name_and_rejects_array_wilayah(): void
    {
        $user = User::factory()->create();
        $name = str_repeat('A', 200);

        $this->actingAs($user)->patch('/profile', [
            'name' => $name, 'email' => $user->email,
        ])->assertSessionHasNoErrors();
        $this->assertSame($name, $user->fresh()->name);

        $this->patch('/profile', [
            'name' => $name, 'email' => $user->email, 'wilayah_id' => [1],
        ])->assertSessionHasErrors('wilayah_id');
    }

    public function test_profile_password_rejects_array_current_password_and_weak_production_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put('/profile/update-password', [
            'current_password' => ['password'],
            'password' => 'Secure123',
            'password_confirmation' => 'Secure123',
        ])->assertSessionHasErrors('current_password');

        app()->detectEnvironment(fn () => 'production');
        try {
            $this->assertTrue(app()->environment('production'));
            $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
                ->put('/profile/update-password', [
                'current_password' => 'password',
                'password' => 'alllowercase',
                'password_confirmation' => 'alllowercase',
            ]);
            $response->assertStatus(302)->assertSessionHasErrors('password');
        } finally {
            app()->detectEnvironment(fn () => 'testing');
        }
    }

    public function test_critical_profile_change_writes_structured_audit_and_notification(): void
    {
        $user = User::factory()->create(['status' => User::STATUS_ACTIVE, 'angkatan' => 2020]);

        $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'angkatan' => 2021,
        ])->assertSessionHasNoErrors();

        $this->assertSame(User::STATUS_PENDING, $user->fresh()->status);
        $this->assertSame(1, $user->notifications()->count());
        $this->assertSame(
            ['before' => 2020, 'after' => 2021],
            AuditLog::where('action', 'profile.update')->firstOrFail()->payload_json['angkatan']
        );
    }

    public function test_replacing_local_photo_removes_old_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/old.jpg', 'old');
        $user = User::factory()->create(['photo_url' => '/storage/avatars/old.jpg']);

        $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'photo' => UploadedFile::fake()->image('new.jpg'),
        ])->assertSessionHasNoErrors();

        Storage::disk('public')->assertMissing('avatars/old.jpg');
        Storage::disk('public')->assertExists(substr($user->fresh()->photo_url, strlen('/storage/')));
    }

    public function test_replacing_external_photo_does_not_delete_local_avatar(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/keep.jpg', 'keep');
        $user = User::factory()->create(['photo_url' => 'https://example.com/avatar.jpg']);

        $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'photo' => UploadedFile::fake()->image('new.jpg'),
        ])->assertSessionHasNoErrors();

        Storage::disk('public')->assertExists('avatars/keep.jpg');
        $this->assertStringStartsWith('/storage/avatars/', $user->fresh()->photo_url);
    }

    public function test_failed_audit_rolls_back_profile_and_removes_new_photo(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/old.jpg', 'old');
        $user = User::factory()->create(['photo_url' => '/storage/avatars/old.jpg']);
        $originalName = $user->name;
        AuditLog::creating(function () {
            throw new \RuntimeException('audit gagal');
        });

        try {
            $this->withoutExceptionHandling()->actingAs($user)->patch('/profile', [
                'name' => 'Nama Baru',
                'email' => $user->email,
                'photo' => UploadedFile::fake()->image('new.jpg'),
            ]);
            $this->fail('Audit seharusnya gagal.');
        } catch (\RuntimeException $error) {
            $this->assertSame('audit gagal', $error->getMessage());
        } finally {
            AuditLog::flushEventListeners();
        }

        $this->assertSame($originalName, $user->fresh()->name);
        $this->assertSame('/storage/avatars/old.jpg', $user->fresh()->photo_url);
        Storage::disk('public')->assertExists('avatars/old.jpg');
        $this->assertCount(1, Storage::disk('public')->files('avatars'));
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertDatabaseHas('audit_logs', ['action' => 'profile.delete']);
        $this->assertNull($user->fresh());
        $audit = AuditLog::where('action', 'profile.delete')->firstOrFail();
        $this->assertSame($user->id, $audit->entity_id);
        $this->assertSame($user->email, $audit->payload_json['email']);
    }

    public function test_failed_delete_audit_keeps_account_and_session(): void
    {
        $user = User::factory()->create();
        AuditLog::creating(function () {
            throw new \RuntimeException('audit gagal');
        });

        try {
            $this->withoutExceptionHandling()->actingAs($user)->delete('/profile', ['password' => 'password']);
            $this->fail('Audit seharusnya gagal.');
        } catch (\RuntimeException $error) {
            $this->assertSame('audit gagal', $error->getMessage());
        } finally {
            AuditLog::flushEventListeners();
        }

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
