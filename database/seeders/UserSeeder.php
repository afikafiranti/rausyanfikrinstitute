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
            ['email' => 'afika@rausyanfikr.com'],
            [
                'name' => 'Nur Afika Firanti',
                'password' => Hash::make('password123'),
                'email_verified_at' => $now,
                'phone' => '081200000001',
                'angkatan' => 2020,
                'pekerjaan' => 'Admin Sistem',
                'wilayah_id' => 1,
                'level_id' => 1,
                'status' => 'active',
                'tempat_lahir' => 'Palopo',
                'tanggal_lahir' => '1990-01-01',
                'pendidikan_terakhir' => 'S1 Informatika',
                'kampus' => 'Universitas Cokroaminoto Palopo',
                'status_pernikahan' => 'Lajang',
                'consent_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
        $super->roles()->syncWithoutDetaching([$roleId('super_admin')]);

        // 2) ADMIN
        $admin = User::query()->updateOrCreate(
            ['email' => 'arif@rausyanfikr.com'],
            [
                'name' => 'Arif Husain',
                'password' => Hash::make('password123'),
                'email_verified_at' => $now,
                'phone' => '081200000002',
                'angkatan' => 2021,
                'pekerjaan' => 'Administrator',
                'wilayah_id' =>2,
                'level_id' => 2,
                'status' => 'active',
                'tempat_lahir' => 'Mamuju',
                'tanggal_lahir' => '1992-05-15',
                'pendidikan_terakhir' => 'S1 Teknik Informatika',
                'kampus' => 'Universitas Islam Makassar',
                'status_pernikahan' => 'Lajang',
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
                'name' => 'Kahar Ali Husain',
                'password' => Hash::make('password123'),
                'email_verified_at' => $now,
                'phone' => '081200000003',
                'angkatan' => 2022,
                'pekerjaan' => 'Wiraswasta',
                'wilayah_id' => 3,
                'level_id' => 3,
                'status' => 'active',
                'tempat_lahir' => 'Luwu',
                'tanggal_lahir' => '1993-09-09',
                'pendidikan_terakhir' => 'S1 Hukum',
                'kampus' => 'Universitas Negeri Makassar',
                'status_pernikahan' => 'Janda',
                'consent_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
        $koorda->roles()->syncWithoutDetaching([$roleId('koorda')]);

        // 4) ALUMNI
        $alumni = User::query()->updateOrCreate(
            ['email' => 'nadia@rausyanfikr.test'],
            [
                'name' => 'Nadia Waris',
                'password' => Hash::make('password123'),
                'email_verified_at' => $now,
                'phone' => '081200000004',
                'angkatan' => 2023,
                'pekerjaan' => 'Mahasiswa',
                'wilayah_id' => 2,
                'level_id' => 4,
                'status' => 'active',
                'tempat_lahir' => 'Makassar',
                'tanggal_lahir' => '1998-12-20',
                'pendidikan_terakhir' => 'SMA',
                'kampus' => null,
                'status_pernikahan' => 'Lajang',
                'consent_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
        $alumni->roles()->syncWithoutDetaching([$roleId('alumni')]);
        
        // 5) ALUMNI
        $alumni = User::query()->updateOrCreate(
            ['email' => 'wawan@rausyanfikr.test'],
            [
                'name' => 'Wawan',
                'password' => Hash::make('password123'),
                'email_verified_at' => $now,
                'phone' => '081200000004',
                'angkatan' => 2023,
                'pekerjaan' => 'Mahasiswa',
                'wilayah_id' => 2,
                'level_id' => 4,
                'status' => 'active',
                'tempat_lahir' => 'Bone',
                'tanggal_lahir' => '1998-12-20',
                'pendidikan_terakhir' => 'SMA',
                'kampus' => null,
                'status_pernikahan' => 'Lajang',
                'consent_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
        $alumni->roles()->syncWithoutDetaching([$roleId('alumni')]);
        
        // 6) ALUMNI
        $alumni = User::query()->updateOrCreate(
            ['email' => 'fatryan@rausyanfikr.test'],
            [
                'name' => 'Fatryan',
                'password' => Hash::make('password123'),
                'email_verified_at' => $now,
                'phone' => '081200000004',
                'angkatan' => 2023,
                'pekerjaan' => 'Mahasiswa',
                'wilayah_id' => 2,
                'level_id' => 4,
                'status' => 'active',
                'tempat_lahir' => 'Selayar',
                'tanggal_lahir' => '1998-12-20',
                'pendidikan_terakhir' => 'S1',
                'kampus' => null,
                'status_pernikahan' => 'Lajang',
                'consent_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
        $alumni->roles()->syncWithoutDetaching([$roleId('alumni')]);
        
    }
}
