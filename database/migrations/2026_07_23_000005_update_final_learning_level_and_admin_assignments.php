<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $levels = [
            1 => ['name' => 'Level 1', 'description' => 'dasar'],
            2 => ['name' => 'Level 2', 'description' => 'menengah'],
            3 => ['name' => 'Level 3', 'description' => 'lanjutan'],
            4 => ['name' => 'Level 4', 'description' => 'akhir'],
        ];

        foreach ($levels as $id => $level) {
            DB::table('levels')
                ->where('id', $id)
                ->update([
                    'name' => $level['name'],
                    'description' => $level['description'],
                    'updated_at' => now(),
                ]);
        }

        $adminRoleIds = DB::table('roles')
            ->whereIn('name', ['super_admin', 'admin'])
            ->pluck('id');

        $adminUserIds = DB::table('role_user')
            ->whereIn('role_id', $adminRoleIds)
            ->pluck('user_id');

        DB::table('users')
            ->whereIn('id', $adminUserIds)
            ->update([
                'level_id' => 4,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('levels')
            ->where('id', 4)
            ->update([
                'description' => 'akhir',
                'updated_at' => now(),
            ]);
    }
};
