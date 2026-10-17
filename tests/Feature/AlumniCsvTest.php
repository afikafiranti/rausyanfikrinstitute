<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use RuntimeException;
use Tests\TestCase;

class AlumniCsvTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_imports_pending_alumni_without_role_or_level_escalation(): void
    {
        $region = Wilayah::create(['name' => 'Jakarta']);
        $admin = $this->account('admin');
        $csv = "name,email,wilayah_id,phone,angkatan,pekerjaan\n".
            "Nadia,nadia@example.test,{$region->id},08123456789,2020,Guru\n";

        $this->actingAs($admin)->post(route('alumni.csv.import'), ['file' => $this->upload($csv)])
            ->assertOk()->assertSee('1 alumni berhasil diimpor');

        $alumni = User::where('email', 'nadia@example.test')->firstOrFail();
        $this->assertSame(User::STATUS_PENDING, $alumni->status);
        $this->assertNull($alumni->level_id);
        $this->assertNull($alumni->email_verified_at);
        $this->assertTrue($alumni->hasRole('alumni'));
        $this->assertSame($region->id, $alumni->wilayah_id);
        $this->assertNotSame('', $alumni->password);
        $this->assertSame('alumni.import', AuditLog::where('entity_id', $alumni->id)->sole()->action);
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_imports_region_name_case_insensitively(): void
    {
        $region = Wilayah::create(['name' => 'Jakarta Selatan']);
        $admin = $this->account('admin');
        $csv = "name,email,wilayah,phone,angkatan,pekerjaan\n".
            "Nadia,nadia@example.test,  jakarta selatan  ,08123456789,2020,Guru\n";

        $this->actingAs($admin)->post(route('alumni.csv.import'), ['file' => $this->upload($csv)])
            ->assertOk()->assertSee('1 alumni berhasil diimpor');
        $this->assertDatabaseHas('users', ['email' => 'nadia@example.test', 'wilayah_id' => $region->id]);
    }

    public function test_unknown_and_ambiguous_region_names_are_reported_per_row(): void
    {
        Wilayah::create(['name' => 'Jakarta']);
        Wilayah::create(['name' => 'JAKARTA']);
        $admin = $this->account('admin');
        $csv = "name,email,wilayah,phone,angkatan,pekerjaan\n".
            "Ambiguous,ambiguous@example.test,jakarta,,,\n".
            "Unknown,unknown@example.test,Bogor,,,\n";

        $this->actingAs($admin)->post(route('alumni.csv.import'), ['file' => $this->upload($csv)])
            ->assertOk()->assertSee('Baris 2')->assertSee('Baris 3');
        $this->assertDatabaseMissing('users', ['email' => 'ambiguous@example.test']);
        $this->assertDatabaseMissing('users', ['email' => 'unknown@example.test']);
    }

    public function test_koorda_named_import_stays_in_own_region(): void
    {
        $own = Wilayah::create(['name' => 'Jakarta']);
        Wilayah::create(['name' => 'Bandung']);
        $koorda = $this->account('koorda', $own->id);
        $csv = "name,email,wilayah,phone,angkatan,pekerjaan\n".
            "Other,other@example.test,Bandung,,,\n".
            "Own,own@example.test, jakarta ,,,\n".
            "Blank,blank@example.test,,,,\n";

        $this->actingAs($koorda)->post(route('alumni.csv.import'), ['file' => $this->upload($csv)])
            ->assertOk()->assertSee('2 alumni berhasil diimpor')->assertSee('Baris 2');
        $this->assertDatabaseMissing('users', ['email' => 'other@example.test']);
        $this->assertDatabaseHas('users', ['email' => 'own@example.test', 'wilayah_id' => $own->id]);
        $this->assertDatabaseHas('users', ['email' => 'blank@example.test', 'wilayah_id' => $own->id]);
    }

    public function test_invalid_header_rejects_entire_file_before_writing(): void
    {
        $admin = $this->account('admin');
        $csv = "name,email,wilayah_id,phone,angkatan,pekerjaan,role\n".
            "Nadia,nadia@example.test,1,,,Guru,admin\n";

        $this->actingAs($admin)->from(route('alumni.csv.index'))
            ->post(route('alumni.csv.import'), ['file' => $this->upload($csv)])
            ->assertSessionHasErrors('file');
        $this->assertDatabaseMissing('users', ['email' => 'nadia@example.test']);
    }

    public function test_empty_header_returns_validation_error(): void
    {
        $admin = $this->account('admin');
        $this->actingAs($admin)->post(route('alumni.csv.import'), ['file' => $this->upload("\n")])
            ->assertSessionHasErrors('file');
    }

    public function test_bad_row_is_reported_but_row_limit_rejects_file_without_partial_import(): void
    {
        $region = Wilayah::create(['name' => 'Jakarta']);
        $admin = $this->account('admin');
        $header = "name,email,wilayah_id,phone,angkatan,pekerjaan\n";
        $first = "Valid,valid@example.test,{$region->id},,,\n";
        $bad = "Broken,broken@example.test,{$region->id}\n";

        $this->actingAs($admin)->post(route('alumni.csv.import'), ['file' => $this->upload($header.$first."\n".$bad)])
            ->assertOk()->assertSee('1 alumni berhasil diimpor')->assertSee('Baris 4');
        $this->assertDatabaseHas('users', ['email' => 'valid@example.test']);
        $this->assertDatabaseMissing('users', ['email' => 'broken@example.test']);

        $many = $header;
        for ($i = 0; $i < 201; $i++) {
            $many .= "Person {$i},person{$i}@example.test,{$region->id},,,\n";
        }
        $this->post(route('alumni.csv.import'), ['file' => $this->upload($many)])
            ->assertSessionHasErrors('file');
        $this->assertDatabaseMissing('users', ['email' => 'person0@example.test']);
    }

    public function test_zero_angkatan_is_preserved(): void
    {
        $region = Wilayah::create(['name' => 'Jakarta']);
        $admin = $this->account('admin');
        $csv = "name,email,wilayah_id,phone,angkatan,pekerjaan\n".
            "Nadia,nadia@example.test,{$region->id},,0,Guru\n";

        $this->actingAs($admin)->post(route('alumni.csv.import'), ['file' => $this->upload($csv)])
            ->assertOk()->assertSee('1 alumni berhasil diimpor');
        $this->assertSame(0, User::where('email', 'nadia@example.test')->firstOrFail()->angkatan);
    }

    public function test_audit_failure_rolls_back_imported_account_and_role(): void
    {
        $region = Wilayah::create(['name' => 'Jakarta']);
        $admin = $this->account('admin');
        $csv = "name,email,wilayah_id,phone,angkatan,pekerjaan\n".
            "Nadia,nadia@example.test,{$region->id},,,\n";
        AuditLog::creating(fn () => throw new RuntimeException('Audit unavailable'));
        try {
            $this->actingAs($admin)->post(route('alumni.csv.import'), ['file' => $this->upload($csv)])
                ->assertInternalServerError();
            $this->assertDatabaseMissing('users', ['email' => 'nadia@example.test']);
        } finally {
            AuditLog::flushEventListeners();
        }
    }

    public function test_invalid_rows_are_reported_while_valid_rows_are_imported(): void
    {
        $region = Wilayah::create(['name' => 'Jakarta']);
        $admin = $this->account('admin');
        $csv = "name,email,wilayah_id,phone,angkatan,pekerjaan\n".
            "Valid,valid@example.test,{$region->id},,2021,Guru\n".
            "Invalid,invalid@example.test,{$region->id},,abc,Guru\n".
            "Duplicate,valid@example.test,{$region->id},,2022,Guru\n";

        $this->actingAs($admin)->post(route('alumni.csv.import'), ['file' => $this->upload($csv)])
            ->assertOk()->assertSee('1 alumni berhasil diimpor')
            ->assertSee('Baris 3')->assertSee('Baris 4');
        $this->assertDatabaseHas('users', ['email' => 'valid@example.test']);
        $this->assertDatabaseMissing('users', ['email' => 'invalid@example.test']);
    }

    public function test_koorda_cannot_import_another_region_and_cannot_export_it(): void
    {
        $own = Wilayah::create(['name' => 'Sendiri']);
        $other = Wilayah::create(['name' => 'Lain']);
        $koorda = $this->account('koorda', $own->id);
        $csv = "name,email,wilayah_id,phone,angkatan,pekerjaan\n".
            "Other,other@example.test,{$other->id},,,\n".
            "Own,own@example.test,,12345,,\n";
        $this->actingAs($koorda)->post(route('alumni.csv.import'), ['file' => $this->upload($csv)])
            ->assertOk()->assertSee('Baris 2');
        $this->assertDatabaseMissing('users', ['email' => 'other@example.test']);
        $this->assertDatabaseHas('users', ['email' => 'own@example.test', 'wilayah_id' => $own->id]);

        User::factory()->create(['name' => 'Other Region', 'wilayah_id' => $other->id]);
        $csvResponse = $this->get(route('alumni.csv.export'));
        $csvResponse->assertOk();
        $this->assertStringContainsString('Own', $csvResponse->streamedContent());
        $this->assertStringNotContainsString('Other Region', $csvResponse->streamedContent());
    }

    public function test_export_uses_filters_and_escapes_spreadsheet_formulas(): void
    {
        $region = Wilayah::create(['name' => 'Jakarta']);
        $admin = $this->account('admin');
        User::factory()->create(['name' => '=2+2', 'email' => 'match@example.test', 'wilayah_id' => $region->id]);
        User::factory()->create(['name' => 'Other', 'email' => 'other@example.test', 'wilayah_id' => $region->id]);

        $response = $this->actingAs($admin)->get(route('alumni.csv.export', ['q' => 'match']));
        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBFname,email,wilayah,phone,angkatan,pekerjaan", $content);
        $this->assertStringContainsString('Jakarta', $content);
        $this->assertStringContainsString("'=2+2", $content);
        $this->assertStringNotContainsString('other@example.test', $content);
        $this->assertStringNotContainsString('password', $content);
    }

    public function test_export_ignores_page_size_and_keeps_region_filter(): void
    {
        $region = Wilayah::create(['name' => 'Jakarta']);
        $other = Wilayah::create(['name' => 'Bandung']);
        $admin = $this->account('admin');
        for ($i = 0; $i < 16; $i++) {
            User::factory()->create(['name' => 'Match '.$i, 'email' => "match{$i}@example.test", 'wilayah_id' => $region->id]);
        }
        User::factory()->create(['name' => 'Match Outside', 'email' => 'outside@example.test', 'wilayah_id' => $other->id]);

        $response = $this->actingAs($admin)->get(route('alumni.csv.export', [
            'q' => 'Match', 'wilayah_id' => $region->id, 'per_page' => 5, 'page' => 2,
        ]));
        $response->assertOk();
        $content = $response->streamedContent();
        for ($i = 0; $i < 16; $i++) {
            $this->assertStringContainsString("match{$i}@example.test", $content);
        }
        $this->assertStringNotContainsString('outside@example.test', $content);
    }

    public function test_existing_phone_is_reported_without_overwriting_account(): void
    {
        $region = Wilayah::create(['name' => 'Jakarta']);
        $admin = $this->account('admin');
        User::factory()->create(['phone' => '08123456789']);
        $csv = "name,email,wilayah_id,phone,angkatan,pekerjaan\n".
            "Nadia,nadia@example.test,{$region->id},08123456789,,\n";

        $this->actingAs($admin)->post(route('alumni.csv.import'), ['file' => $this->upload($csv)])
            ->assertOk()->assertSee('Baris 2');
        $this->assertDatabaseMissing('users', ['email' => 'nadia@example.test']);
    }

    public function test_alumni_and_koorda_without_region_cannot_use_csv(): void
    {
        $alumni = $this->account('alumni');
        $this->actingAs($alumni)->get(route('alumni.csv.index'))->assertForbidden();
        $this->get(route('alumni.csv.export'))->assertForbidden();
        $this->post(route('alumni.csv.import'), ['file' => $this->upload('')])->assertForbidden();

        $koorda = $this->account('koorda');
        $this->actingAs($koorda)->get(route('alumni.csv.index'))->assertForbidden();
        $this->get(route('alumni.csv.export'))->assertForbidden();
    }

    public function test_guest_and_inactive_admin_cannot_use_csv(): void
    {
        $this->get(route('alumni.csv.index'))->assertRedirect();
        $this->get(route('alumni.csv.export'))->assertRedirect();
        $this->post(route('alumni.csv.import'), ['file' => $this->upload('')])->assertRedirect();

        $admin = $this->account('admin');
        foreach ([User::STATUS_PENDING, User::STATUS_SUSPENDED] as $status) {
            $admin->update(['status' => $status]);
            $this->actingAs($admin)->get(route('alumni.csv.index'))->assertForbidden();
            $this->get(route('alumni.csv.export'))->assertForbidden();
            $this->post(route('alumni.csv.import'), ['file' => $this->upload('')])->assertForbidden();
        }
    }

    public function test_unclosed_quote_rejects_file_before_any_account_is_written(): void
    {
        $region = Wilayah::create(['name' => 'Jakarta']);
        $csv = "name,email,wilayah_id,phone,angkatan,pekerjaan\n".
            "First,first@example.test,{$region->id},,,\n".
            "\"Broken,bad@example.test,{$region->id},,,\n".
            "Next,next@example.test,{$region->id},,,\n";

        $this->actingAs($this->account('admin'))->post(route('alumni.csv.import'), ['file' => $this->upload($csv)])
            ->assertSessionHasErrors('file');
        $this->assertDatabaseMissing('users', ['email' => 'first@example.test']);
        $this->assertDatabaseMissing('users', ['email' => 'next@example.test']);
    }

    public function test_quoted_commas_quotes_and_multiline_fields_are_preserved(): void
    {
        $region = Wilayah::create(['name' => 'Jakarta']);
        $csv = "name,email,wilayah_id,phone,angkatan,pekerjaan\r\n".
            "\"Nama, \"\"Alumni\"\"\",quoted@example.test,{$region->id},,,\"Guru\nPenulis\"\r\n";

        $this->actingAs($this->account('admin'))->post(route('alumni.csv.import'), ['file' => $this->upload($csv)])
            ->assertOk()->assertSee('1 alumni berhasil diimpor');
        $this->assertDatabaseHas('users', [
            'email' => 'quoted@example.test', 'name' => 'Nama, "Alumni"', 'pekerjaan' => "Guru\nPenulis",
        ]);
    }

    private function account(string $role, ?int $regionId = null): User
    {
        $user = User::factory()->create(['status' => User::STATUS_ACTIVE, 'wilayah_id' => $regionId]);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));
        return $user;
    }

    private function upload(string $content): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('alumni.csv', $content);
    }
}
