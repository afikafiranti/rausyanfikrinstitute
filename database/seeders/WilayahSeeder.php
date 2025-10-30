<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('wilayah')->upsert([
            ['id' => 1, 'name' => 'Luwu', 'parent_id' => null, 'kode' => 'LUW', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Palopo', 'parent_id' => null, 'kode' => 'PLP', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Makassar', 'parent_id' => null, 'kode' => 'MKS', 'created_at' => $now, 'updated_at' => $now],
            // Tambahkan contoh lain jika diperlukan, misal:
            // ['id' => 2, 'name' => 'Belopa', 'parent_id' => 1, 'kode' => 'BEL', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['name', 'parent_id', 'kode', 'updated_at']);
    }
}
