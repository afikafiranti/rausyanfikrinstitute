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

        DB::table('levels')->upsert([
            ['id' => 1, 'name' => 'Level 1', 'description' => null, 'criteria_json' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Level 2', 'description' => null, 'criteria_json' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Level 3', 'description' => null, 'criteria_json' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['name', 'description', 'criteria_json', 'is_active', 'updated_at']);
    }
}
