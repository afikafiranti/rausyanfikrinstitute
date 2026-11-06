<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SchemaAndExampleSeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_examples_cover_workflows_and_reseeding_preserves_existing_accounts(): void
    {
        $this->seed(DatabaseSeeder::class);

        $account = User::query()->where('email', 'afika@rausyanfikr.test')->firstOrFail();
        $account->update(['name' => 'Nama pengguna diperbarui', 'password' => bcrypt('rahasia-baru')]);
        $counts = [];
        foreach (['users', 'role_user', 'posts', 'galleries', 'playlists', 'videos', 'kajian_reports'] as $table) {
            $counts[$table] = DB::table($table)->count();
        }

        $this->seed(DatabaseSeeder::class);

        foreach ($counts as $table => $count) {
            $this->assertSame($count, DB::table($table)->count(), $table);
        }
        $this->assertSame('Nama pengguna diperbarui', $account->fresh()->name);
        $this->assertTrue(password_verify('rahasia-baru', $account->fresh()->password));

        foreach (['article', 'reflection'] as $type) {
            $this->assertSame(1, DB::table('posts')->where('type', $type)->whereNotNull('published_at')->count());
            $this->assertSame(1, DB::table('posts')->where('type', $type)->whereNull('published_at')->count());
        }
        foreach (['draft', 'submitted', 'approved', 'rejected'] as $status) {
            $this->assertSame(1, DB::table('kajian_reports')->where('status', $status)->count());
        }
        $this->assertSame(2, DB::table('role_user')->where('user_id', User::where('email', 'contoh.multi-role@rausyanfikr.test')->value('id'))->count());
        $this->assertSame(0, DB::table('role_user')->where('user_id', User::where('email', 'contoh.tanpa-role@rausyanfikr.test')->value('id'))->count());
        $this->assertNull(User::where('email', 'contoh.tanpa-wilayah@rausyanfikr.test')->value('wilayah_id'));
        $this->assertSame(1, DB::table('users')->where('status', 'pending')->count());
        $this->assertSame(1, DB::table('users')->where('status', 'suspended')->count());
        $this->assertSame(1, DB::table('playlists')->where('is_active', false)->count());
        $this->assertSame(2, DB::table('videos')->count());
    }

    public function test_schema_has_unique_phone_and_no_removed_private_or_deferred_tables(): void
    {
        $this->assertTrue(Schema::hasIndex('users', 'users_phone_unique'));
        $this->assertFalse(Schema::hasIndex('users', 'idx_users_email'));
        $this->assertFalse(Schema::hasIndex('users', 'idx_users_angkatan'));
        $this->assertFalse(Schema::hasTable('member_reflections'));
        $this->assertFalse(Schema::hasTable('video_progress'));
        $phoneIndexes = array_values(array_filter(DB::select('SHOW INDEX FROM users'),
            fn ($index) => $index->Key_name === 'users_phone_unique'));
        $this->assertCount(1, $phoneIndexes);
        $this->assertSame(0, (int) $phoneIndexes[0]->Non_unique);
        $this->assertSame('phone', $phoneIndexes[0]->Column_name);

        $reportForeignKeys = DB::table('information_schema.REFERENTIAL_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'kajian_reports')
            ->pluck('DELETE_RULE', 'CONSTRAINT_NAME');
        $this->assertSame('RESTRICT', $reportForeignKeys['kajian_reports_user_id_foreign']);
        $this->assertSame('RESTRICT', $reportForeignKeys['kajian_reports_reviewed_by_foreign']);

        $this->seed(DatabaseSeeder::class);
        $this->expectException(\Illuminate\Database\QueryException::class);
        User::query()->create([
            'name' => 'Contoh telepon duplikat',
            'email' => 'contoh.telepon-duplikat@rausyanfikr.test',
            'password' => bcrypt('password123'),
            'phone' => '081200000001',
        ]);
    }
}
