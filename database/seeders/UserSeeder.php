<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Pastikan role tersedia (fallback jika RoleSeeder belum pernah jalan)
        $roleNames = ['alumni','koorda','admin','super_admin'];
        foreach ($roleNames as $name) {
            Role::query()->firstOrCreate(
                ['name' => $name],
                ['created_at' => $now, 'updated_at' => $now]
            );
        }

        // Pastikan Wilayah ID=1 ada (fallback)
        DB::table('wilayah')->updateOrInsert(
            ['id' => 1],
            ['name' => 'Luwu', 'parent_id' => null, 'kode' => 'LUW', 'updated_at' => $now, 'created_at' => $now]
        );

        // Pastikan Level ID=1 ada (fallback)
        DB::table('levels')->updateOrInsert(
            ['id' => 1],
            ['name' => 'Level 1', 'is_active' => true, 'updated_at' => $now, 'created_at' => $now]
        );

        // Helper ambil id role
        $roleId = fn(string $r) => Role::query()->where('name', $r)->value('id');

        // 1) SUPER ADMIN
        $super = User::query()->updateOrCreate(
            ['email' => 'superadmin@rausyanfikr.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => $now,
                'phone' => '081200000001',
                'angkatan' => 2020,
                'pekerjaan' => 'Admin Sistem',
                'wilayah_id' => 1,
                'level_id' => 1,
                'status' => 'active',
                'tempat_lahir' => 'Makassar',
                'tanggal_lahir' => '1990-01-01',
                'pendidikan_terakhir' => 'S2 Informatika',
                'kampus' => 'Universitas Hasanuddin',
                'status_pernikahan' => 'Menikah',
                'consent_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
        $super->roles()->syncWithoutDetaching([$roleId('super_admin')]);

        // 2) ADMIN
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@rausyanfikr.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => $now,
                'phone' => '081200000002',
                'angkatan' => 2021,
                'pekerjaan' => 'Administrator',
                'wilayah_id' => 1,
                'level_id' => 1,
                'status' => 'active',
                'tempat_lahir' => 'Palopo',
                'tanggal_lahir' => '1992-05-15',
                'pendidikan_terakhir' => 'S1 Teknik Informatika',
                'kampus' => 'Universitas Islam Makassar',
                'status_pernikahan' => 'Belum Menikah',
                'consent_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
        $admin->roles()->syncWithoutDetaching([$roleId('admin')]);

        // 3) KOORDA
        $koorda = User::query()->updateOrCreate(
            ['email' => 'koorda@rausyanfikr.test'],
            [
                'name' => 'Koordinator Daerah',
                'password' => Hash::make('password123'),
                'email_verified_at' => $now,
                'phone' => '081200000003',
                'angkatan' => 2022,
                'pekerjaan' => 'Koordinator',
                'wilayah_id' => 1,
                'level_id' => 1,
                'status' => 'active',
                'tempat_lahir' => 'Luwu',
                'tanggal_lahir' => '1993-09-09',
                'pendidikan_terakhir' => 'S1 Hukum',
                'kampus' => 'Universitas Negeri Makassar',
                'status_pernikahan' => 'Menikah',
                'consent_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
        $koorda->roles()->syncWithoutDetaching([$roleId('koorda')]);

        // 4) ALUMNI
        $alumni = User::query()->updateOrCreate(
            ['email' => 'alumni@rausyanfikr.test'],
            [
                'name' => 'Alumni',
                'password' => Hash::make('password123'),
                'email_verified_at' => $now,
                'phone' => '081200000004',
                'angkatan' => 2023,
                'pekerjaan' => 'Peserta Kajian',
                'wilayah_id' => 1,
                'level_id' => 1,
                'status' => 'active',
                'tempat_lahir' => 'Palopo',
                'tanggal_lahir' => '1998-12-20',
                'pendidikan_terakhir' => 'SMA',
                'kampus' => null,
                'status_pernikahan' => 'Belum Menikah',
                'consent_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
        $alumni->roles()->syncWithoutDetaching([$roleId('alumni')]);
    }
}
