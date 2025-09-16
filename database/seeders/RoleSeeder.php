<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('roles')->upsert([
            ['id' => 1, 'name' => 'alumni',       'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'koorda',       'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'admin',        'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'super_admin',  'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['name', 'updated_at']);
    }
}
