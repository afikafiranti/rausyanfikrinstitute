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
    }

    public function down(): void
    {
        $levels = [
            1 => ['name' => 'Level 1', 'description' => 'superadmin'],
            2 => ['name' => 'Level 2', 'description' => 'admin'],
            3 => ['name' => 'Level 3', 'description' => 'koordinator'],
            4 => ['name' => 'Level 4', 'description' => 'alumni'],
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
    }
};
