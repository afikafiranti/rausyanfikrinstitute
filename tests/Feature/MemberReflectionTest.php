<?php

namespace Tests\Feature;

use App\Models\MemberReflection;
use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberReflectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_managers_do_not_see_report_links_on_private_reflections(): void
    {
        foreach (['admin', 'super_admin', 'koorda'] as $role) {
            foreach ([User::STATUS_PENDING, User::STATUS_SUSPENDED] as $status) {
                $viewer = $this->member($role, ['status' => $status]);
                $this->actingAs($viewer)->get('/refleksi-saya')->assertOk()
                    ->assertDontSee(route('laporan.index'), false)
                    ->assertDontSee(route('laporan.review'), false);
            }
        }
    }

    public function test_alumni_can_save_private_draft_without_a_region(): void
    {
        $alumni = User::factory()->create(['status' => User::STATUS_PENDING, 'wilayah_id' => null]);
        $alumni->roles()->attach(Role::firstOrCreate(['name' => 'alumni']));

        $this->actingAs($alumni)->post('/refleksi-saya', [
            'title' => 'Catatan Kajian',
            'context' => 'kajian',
            'activity_name' => 'Diskusi Ahad',
            'body' => 'Hal yang saya pelajari hari ini.',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $reflection = MemberReflection::query()->sole();
        $this->assertSame($alumni->id, $reflection->user_id);
        $this->assertNull($reflection->wilayah_id);
        $this->assertSame('draft', $reflection->status);
        $this->assertNull($reflection->submitted_at);
        $this->assertSame('Catatan Kajian', $reflection->title);
    }

    public function test_draft_ignores_injected_owner_review_and_region_fields(): void
    {
        $region = Wilayah::create(['name' => 'Luar Wilayah']);
        $owner = $this->member('alumni', ['status' => User::STATUS_PENDING]);
        $other = $this->member('alumni', ['status' => User::STATUS_ACTIVE]);

        $this->actingAs($owner)->post('/refleksi-saya', $this->data() + [
            'user_id' => $other->id,
            'wilayah_id' => $region->id,
            'status' => 'reviewed',
            'submitted_at' => now()->toDateTimeString(),
            'reviewed_by' => $other->id,
            'feedback' => 'Palsu',
        ])->assertRedirect();

        $reflection = MemberReflection::query()->sole();
        $this->assertSame($owner->id, $reflection->user_id);
        $this->assertNull($reflection->wilayah_id);
        $this->assertSame('draft', $reflection->status);
        $this->assertNull($reflection->submitted_at);
        $this->assertNull($reflection->reviewed_by);
        $this->assertNull($reflection->feedback);
    }

    public function test_drafts_are_private_and_owner_can_edit_and_delete_them(): void
    {
        $owner = $this->member('alumni', ['status' => User::STATUS_PENDING]);
        $other = $this->member('alumni', ['status' => User::STATUS_ACTIVE]);
        $mine = $this->reflection($owner, ['title' => 'Catatan Saya']);
        $foreign = $this->reflection($other, ['title' => 'Catatan Pribadi Orang Lain']);

        $this->actingAs($owner)->get('/refleksi-saya')
            ->assertOk()->assertSee('Catatan Saya')->assertDontSee('Catatan Pribadi Orang Lain');
        $this->get('/refleksi-saya/'.$mine->id)->assertOk();
        $this->get('/refleksi-saya/'.$foreign->id)->assertNotFound();
        $this->get('/refleksi-saya/'.$foreign->id.'/edit')->assertNotFound();
        $this->patch('/refleksi-saya/'.$foreign->id, $this->data())->assertNotFound();
        $this->delete('/refleksi-saya/'.$foreign->id)->assertNotFound();
        $this->post('/refleksi-saya/'.$foreign->id.'/kirim')->assertNotFound();

        $admin = $this->member('admin', ['status' => User::STATUS_ACTIVE]);
        $this->actingAs($admin)->get('/refleksi-saya/'.$mine->id)->assertNotFound();
        $this->patch('/refleksi-saya/'.$mine->id, $this->data())->assertNotFound();
        $this->actingAs($owner);

        $this->patch('/refleksi-saya/'.$mine->id, array_merge($this->data(), ['title' => 'Diubah']))
            ->assertRedirect();
        $this->assertSame('Diubah', $mine->fresh()->title);
        $this->delete('/refleksi-saya/'.$mine->id)->assertRedirect();
        $this->assertDatabaseMissing('member_reflections', ['id' => $mine->id]);
    }

    public function test_only_active_alumni_with_region_can_submit_and_snapshot_its_region(): void
    {
        $region = Wilayah::create(['name' => 'Wilayah Aktif']);
        $otherRegion = Wilayah::create(['name' => 'Wilayah Lain']);
        $owner = $this->member('alumni', ['status' => User::STATUS_ACTIVE, 'wilayah_id' => $region->id]);
        $this->assertNull($owner->level_id);
        $reflection = $this->reflection($owner);

        $this->actingAs($owner)->post('/refleksi-saya/'.$reflection->id.'/kirim', [
            'wilayah_id' => $otherRegion->id,
            'status' => 'reviewed',
        ])->assertRedirect();
        $reflection->refresh();
        $this->assertSame('submitted', $reflection->status);
        $this->assertSame($region->id, $reflection->wilayah_id);
        $this->assertNotNull($reflection->submitted_at);

        $this->get('/refleksi-saya/'.$reflection->id)->assertOk();
        $this->get('/refleksi-saya/'.$reflection->id.'/edit')->assertStatus(409);
        $this->patch('/refleksi-saya/'.$reflection->id, $this->data())->assertStatus(409);
        $this->delete('/refleksi-saya/'.$reflection->id)->assertStatus(409);
        $this->post('/refleksi-saya/'.$reflection->id.'/kirim')->assertStatus(409);
    }

    public function test_pending_suspended_and_regionless_alumni_cannot_submit(): void
    {
        $region = Wilayah::create(['name' => 'Wilayah Aktif']);
        foreach ([
            ['status' => User::STATUS_PENDING, 'wilayah_id' => $region->id],
            ['status' => User::STATUS_SUSPENDED, 'wilayah_id' => $region->id],
            ['status' => User::STATUS_ACTIVE, 'wilayah_id' => null],
        ] as $attributes) {
            $owner = $this->member('alumni', $attributes);
            $reflection = $this->reflection($owner);
            $this->actingAs($owner)->post('/refleksi-saya/'.$reflection->id.'/kirim')->assertForbidden();
            $this->assertSame('draft', $reflection->fresh()->status);
        }
    }

    public function test_active_managers_with_region_can_submit_their_own_reflections(): void
    {
        $region = Wilayah::create(['name' => 'Wilayah Pengelola']);
        foreach (['koorda', 'admin', 'super_admin'] as $role) {
            $owner = $this->member($role, ['status' => User::STATUS_ACTIVE, 'wilayah_id' => $region->id]);
            $reflection = $this->reflection($owner);

            $this->actingAs($owner)->post('/refleksi-saya/'.$reflection->id.'/kirim')->assertRedirect();
            $this->assertDatabaseHas('member_reflections', [
                'id' => $reflection->id,
                'user_id' => $owner->id,
                'wilayah_id' => $region->id,
                'status' => 'submitted',
            ]);
        }
    }

    public function test_each_account_role_can_keep_private_drafts_but_roleless_account_cannot(): void
    {
        foreach (['alumni', 'koorda', 'admin', 'super_admin'] as $role) {
            $owner = $this->member($role, ['status' => User::STATUS_PENDING]);
            $this->actingAs($owner)->post('/refleksi-saya', $this->data())->assertRedirect();
            $this->assertDatabaseHas('member_reflections', ['user_id' => $owner->id, 'status' => 'draft']);
        }

        $roleless = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $this->actingAs($roleless)->get('/refleksi-saya')->assertForbidden();
        $this->post('/refleksi-saya', $this->data())->assertForbidden();
    }

    public function test_reviewers_see_only_submitted_or_reviewed_reflections_in_their_scope(): void
    {
        $local = Wilayah::create(['name' => 'Lokal']);
        $remote = Wilayah::create(['name' => 'Luar']);
        $author = $this->member('alumni', ['status' => User::STATUS_ACTIVE]);
        $private = $this->reflection($author, ['title' => 'Draf Rahasia']);
        $localSubmission = $this->reflection($author, ['title' => 'Terkirim Lokal', 'status' => 'submitted', 'wilayah_id' => $local->id]);
        $remoteSubmission = $this->reflection($author, ['title' => 'Terkirim Luar', 'status' => 'submitted', 'wilayah_id' => $remote->id]);
        $reviewed = $this->reflection($author, ['title' => 'Sudah Ditinjau', 'status' => 'reviewed', 'wilayah_id' => $local->id]);

        $koorda = $this->member('koorda', ['status' => User::STATUS_ACTIVE, 'wilayah_id' => $local->id]);
        $this->actingAs($koorda)->get('/tinjau-refleksi')
            ->assertOk()->assertSee('Terkirim Lokal')->assertSee('Sudah Ditinjau')
            ->assertDontSee('Draf Rahasia')->assertDontSee('Terkirim Luar');
        $this->get('/tinjau-refleksi?wilayah_id='.$remote->id)
            ->assertOk()->assertSee('Terkirim Lokal')->assertDontSee('Terkirim Luar');
        $this->get('/tinjau-refleksi/'.$localSubmission->id)->assertOk();
        $this->get('/tinjau-refleksi/'.$reviewed->id)->assertOk();
        $this->get('/tinjau-refleksi/'.$private->id)->assertNotFound();
        $this->get('/tinjau-refleksi/'.$remoteSubmission->id)->assertNotFound();

        $superAdmin = $this->member('super_admin', ['status' => User::STATUS_ACTIVE]);
        $this->actingAs($superAdmin)->get('/tinjau-refleksi')
            ->assertOk()->assertSee('Terkirim Lokal')->assertSee('Terkirim Luar')
            ->assertDontSee('Draf Rahasia');
        $this->get('/tinjau-refleksi/'.$private->id)->assertNotFound();

        $admin = $this->member('admin', ['status' => User::STATUS_ACTIVE]);
        $this->actingAs($admin)->get('/tinjau-refleksi')
            ->assertOk()->assertSee('Terkirim Lokal')->assertSee('Terkirim Luar')
            ->assertDontSee('Draf Rahasia');
        $this->get('/tinjau-refleksi/'.$private->id)->assertNotFound();
    }

    public function test_review_requires_feedback_and_cannot_repeat_or_review_own_submission(): void
    {
        $region = Wilayah::create(['name' => 'Lokal']);
        $author = $this->member('alumni', ['status' => User::STATUS_ACTIVE, 'wilayah_id' => $region->id]);
        $reflection = $this->reflection($author, ['status' => 'submitted', 'wilayah_id' => $region->id]);
        $reviewer = $this->member('koorda', ['status' => User::STATUS_ACTIVE, 'wilayah_id' => $region->id]);

        $this->actingAs($reviewer)->patch('/tinjau-refleksi/'.$reflection->id, ['feedback' => ''])
            ->assertSessionHasErrors('feedback');
        $this->patch('/tinjau-refleksi/'.$reflection->id, ['feedback' => 'Perjelas hubungan dengan materi.'])
            ->assertRedirect();
        $reflection->refresh();
        $this->assertSame('reviewed', $reflection->status);
        $this->assertSame($reviewer->id, $reflection->reviewed_by);
        $this->assertNotNull($reflection->reviewed_at);
        $this->assertSame('Perjelas hubungan dengan materi.', $reflection->feedback);
        $this->patch('/tinjau-refleksi/'.$reflection->id, ['feedback' => 'Coba lagi'])->assertStatus(409);

        $this->actingAs($author)->get('/refleksi-saya/'.$reflection->id)->assertOk();
        $this->get('/refleksi-saya/'.$reflection->id.'/edit')->assertStatus(409);
        $this->patch('/refleksi-saya/'.$reflection->id, $this->data())->assertStatus(409);
        $this->delete('/refleksi-saya/'.$reflection->id)->assertStatus(409);

        $author->roles()->attach(Role::firstOrCreate(['name' => 'admin']));
        $this->actingAs($author)->get('/tinjau-refleksi/'.$reflection->id)->assertNotFound();
        $this->patch('/tinjau-refleksi/'.$reflection->id, ['feedback' => 'Saya sendiri'])->assertNotFound();
    }

    public function test_review_requires_active_manager_and_koorda_region(): void
    {
        $author = $this->member('alumni', ['status' => User::STATUS_ACTIVE]);
        $reflection = $this->reflection($author, ['status' => 'submitted']);
        foreach ([
            $this->member('alumni', ['status' => User::STATUS_ACTIVE]),
            $this->member('koorda', ['status' => User::STATUS_PENDING]),
            $this->member('koorda', ['status' => User::STATUS_ACTIVE, 'wilayah_id' => null]),
        ] as $viewer) {
            $this->actingAs($viewer)->get('/tinjau-refleksi')->assertForbidden();
            $this->patch('/tinjau-refleksi/'.$reflection->id, ['feedback' => 'Tidak berwenang'])->assertForbidden();
        }
    }

    public function test_invalid_content_and_guest_requests_do_not_create_reflections(): void
    {
        $this->get('/refleksi-saya')->assertRedirect('/login');
        $this->post('/refleksi-saya', $this->data())->assertRedirect('/login');

        $owner = $this->member('alumni', ['status' => User::STATUS_ACTIVE]);
        $this->actingAs($owner)->post('/refleksi-saya', [
            'title' => str_repeat('a', 181),
            'context' => 'illegal',
            'body' => '',
        ])->assertSessionHasErrors(['title', 'context', 'body']);
        $this->post('/refleksi-saya', array_merge($this->data(), ['body' => str_repeat('x', 20001)]))
            ->assertSessionHasErrors('body');
        $this->assertDatabaseCount('member_reflections', 0);
    }

    private function member(string $role, array $attributes): User
    {
        $user = User::factory()->create($attributes);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));

        return $user;
    }

    private function reflection(User $owner, array $attributes = []): MemberReflection
    {
        return MemberReflection::create(array_merge($this->data(), ['user_id' => $owner->id], $attributes));
    }

    private function data(): array
    {
        return [
            'title' => 'Catatan Kajian',
            'context' => 'kajian',
            'activity_name' => 'Diskusi Ahad',
            'body' => 'Hal yang saya pelajari hari ini.',
        ];
    }
}
