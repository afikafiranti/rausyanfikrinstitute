<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlumniEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_edit_form_for_alumni_user(): void
    {
        $admin = $this->userWithRole('admin');
        $alumni = $this->userWithRole('alumni', ['name' => 'Andi Puji Lestari']);

        $response = $this
            ->actingAs($admin)
            ->get(route('alumni.edit', $alumni));

        $response
            ->assertOk()
            ->assertSee('Edit Data Alumni')
            ->assertSee('Andi Puji Lestari')
            ->assertSee('name="name"', false)
            ->assertSee(route('alumni.update', $alumni), false);
    }

    public function test_admin_can_update_alumni_profile_data(): void
    {
        $admin = $this->userWithRole('admin');
        $wilayah = Wilayah::create(['name' => 'Palopo']);
        $alumni = $this->userWithRole('alumni', [
            'email' => 'andi-lama@example.test',
            'phone' => '081111111111',
        ]);

        $response = $this
            ->actingAs($admin)
            ->from(route('alumni.edit', $alumni))
            ->patch(route('alumni.update', $alumni), [
                'name' => 'Andi Puji Lestari',
                'email' => 'andi-baru@example.test',
                'phone' => '085312341234',
                'angkatan' => '2019',
                'pekerjaan' => 'Dosen',
                'wilayah_id' => $wilayah->id,
                'tempat_lahir' => 'Palopo',
                'tanggal_lahir' => '1995-10-07',
                'pendidikan_terakhir' => 'S2',
                'kampus' => 'Universitas Gadjah Mada',
                'status_pernikahan' => 'lajang',
                'ab' => 'iya',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('alumni.show', $alumni, absolute: false))
            ->assertSessionHas('success');

        $alumni->refresh();

        $this->assertSame('Andi Puji Lestari', $alumni->name);
        $this->assertSame('andi-baru@example.test', $alumni->email);
        $this->assertSame('085312341234', $alumni->phone);
        $this->assertSame('2019', (string) $alumni->angkatan);
        $this->assertSame('Dosen', $alumni->pekerjaan);
        $this->assertSame($wilayah->id, $alumni->wilayah_id);
        $this->assertSame('Palopo', $alumni->tempat_lahir);
        $this->assertSame('1995-10-07', $alumni->tanggal_lahir);
        $this->assertSame('S2', $alumni->pendidikan_terakhir);
        $this->assertSame('Universitas Gadjah Mada', $alumni->kampus);
        $this->assertSame('lajang', $alumni->status_pernikahan);
        $this->assertSame('iya', $alumni->ab);
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'action' => 'alumni.update',
            'entity_type' => 'user',
            'entity_id' => $alumni->id,
        ]);
    }

    public function test_super_admin_can_update_koorda_profile_data(): void
    {
        $superAdmin = $this->userWithRole('super_admin');
        $koorda = $this->userWithRole('koorda', ['email' => 'koorda-lama@example.test']);

        $response = $this
            ->actingAs($superAdmin)
            ->from(route('alumni.edit', $koorda))
            ->patch(route('alumni.update', $koorda), [
                'name' => 'Diah Eka Pratika',
                'email' => 'koorda-baru@example.test',
                'phone' => null,
                'angkatan' => null,
                'pekerjaan' => 'Koordinator',
                'wilayah_id' => null,
                'tempat_lahir' => null,
                'tanggal_lahir' => null,
                'pendidikan_terakhir' => null,
                'kampus' => null,
                'status_pernikahan' => null,
                'ab' => 'tidak',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('alumni.show', $koorda, absolute: false));

        $this->assertSame('Diah Eka Pratika', $koorda->refresh()->name);
        $this->assertSame('koorda-baru@example.test', $koorda->email);
    }

    public function test_koorda_cannot_edit_alumni_profile_data(): void
    {
        $koordaActor = $this->userWithRole('koorda');
        $alumni = $this->userWithRole('alumni', ['name' => 'Nama Lama']);

        $response = $this
            ->actingAs($koordaActor)
            ->from(route('alumni.show', $alumni))
            ->patch(route('alumni.update', $alumni), [
                'name' => 'Nama Baru',
                'email' => $alumni->email,
            ]);

        $response
            ->assertSessionHasErrors('edit')
            ->assertRedirect(route('alumni.show', $alumni, absolute: false));

        $this->assertSame('Nama Lama', $alumni->refresh()->name);
    }

    public function test_admin_cannot_edit_admin_or_super_admin_from_alumni_feature(): void
    {
        $admin = $this->userWithRole('admin');
        $otherAdmin = $this->userWithRole('admin', ['name' => 'Admin Aman']);
        $superAdmin = $this->userWithRole('super_admin', ['name' => 'Super Admin Aman']);

        $this
            ->actingAs($admin)
            ->from(route('alumni.show', $otherAdmin))
            ->patch(route('alumni.update', $otherAdmin), [
                'name' => 'Admin Berubah',
                'email' => $otherAdmin->email,
            ])
            ->assertSessionHasErrors('edit')
            ->assertRedirect(route('alumni.show', $otherAdmin, absolute: false));

        $this
            ->actingAs($admin)
            ->from(route('alumni.show', $superAdmin))
            ->patch(route('alumni.update', $superAdmin), [
                'name' => 'Super Admin Berubah',
                'email' => $superAdmin->email,
            ])
            ->assertSessionHasErrors('edit')
            ->assertRedirect(route('alumni.show', $superAdmin, absolute: false));

        $this->assertSame('Admin Aman', $otherAdmin->refresh()->name);
        $this->assertSame('Super Admin Aman', $superAdmin->refresh()->name);
    }

    private function userWithRole(string $roleName, array $attributes = []): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);

        $user = User::factory()->create(array_merge([
            'status' => User::STATUS_ACTIVE,
        ], $attributes));

        $user->roles()->attach($role->id);

        return $user;
    }
}
