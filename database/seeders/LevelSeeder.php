<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Bersihkan dulu agar auto increment dan data lama tidak bentrok
        DB::table('levels')->delete();

        // Insert/update data level sesuai standar Rausyan Fikr
        DB::table('levels')->upsert([
            ['id' => 1, 'name' => 'Level 1', 'description' => 'superadmin', 'criteria_json' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Level 2', 'description' => 'admin', 'criteria_json' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Level 3', 'description' => 'koordinator', 'criteria_json' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Level 4', 'description' => 'alumni', 'criteria_json' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['name', 'description', 'criteria_json', 'is_active', 'updated_at']);
    }
}
