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

        // Level adalah tingkat pembelajaran/materi, bukan role hak akses sistem.
        DB::table('levels')->insertOrIgnore([
            ['id' => 1, 'name' => 'Level 1', 'description' => 'dasar', 'criteria_json' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Level 2', 'description' => 'menengah', 'criteria_json' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Level 3', 'description' => 'lanjutan', 'criteria_json' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Level 4', 'description' => 'akhir', 'criteria_json' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
