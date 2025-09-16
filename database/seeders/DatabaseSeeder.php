<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\LevelSeeder;
use Database\Seeders\WilayahSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LevelSeeder::class,
            RoleSeeder::class,
            WilayahSeeder::class,
            UserSeeder::class
        ]);
    }
}
