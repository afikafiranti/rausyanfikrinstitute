<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $now = Carbon::now();

        $this->call([RoleSeeder::class, LevelSeeder::class, WilayahSeeder::class]);

        // Helper ambil id role
        $roleId = fn(string $r) => Role::query()->where('name', $r)->value('id');

        // 1) SUPER ADMIN
        $super = User::query()->firstOrCreate(
            ['email' => 'afika@rausyanfikr.com'],
            [
                'name' => 'Nur Afika Firanti',
                'password' => Hash::make('password123'),
                'email_verified_at' => $now,
                'phone' => '081200000001',
                'angkatan' => 2020,
                'pekerjaan' => 'Admin Sistem',
                'wilayah_id' => 1,
                'level_id' => 4,
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
        if ($super->wasRecentlyCreated) {
            $super->roles()->attach($roleId('super_admin'));
        }

        // 2) ADMIN
        $admin = User::query()->firstOrCreate(
            ['email' => 'arif@rausyanfikr.com'],
            [
                'name' => 'Arif Husain',
                'password' => Hash::make('password123'),
                'email_verified_at' => $now,
                'phone' => '081200000002',
                'angkatan' => 2021,
                'pekerjaan' => 'Administrator',
                'wilayah_id' => 2,
                'level_id' => 4,
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
        if ($admin->wasRecentlyCreated) {
            $admin->roles()->attach($roleId('admin'));
        }

        // 3) KOORDA
        $koorda = User::query()->firstOrCreate(
            ['email' => 'koorda@rausyanfikr.test'],
            [
                'name' => 'Diah Eka Pratika',
                'password' => Hash::make('password123'),
                'email_verified_at' => $now,
                'phone' => '081200000003',
                'angkatan' => 2022,
                'pekerjaan' => 'Wiraswasta',
                'wilayah_id' => 3,
                'level_id' => 3,
                'status' => 'active',
                'tempat_lahir' => 'Gowa',
                'tanggal_lahir' => '1993-09-09',
                'pendidikan_terakhir' => 'S1 Manajemen',
                'kampus' => 'Universitas Negeri Makassar',
                'status_pernikahan' => 'Menikah',
                'consent_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
        if ($koorda->wasRecentlyCreated) {
            $koorda->roles()->attach($roleId('koorda'));
        }

        // 4) ALUMNI
        $alumni = User::query()->firstOrCreate(
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
        if ($alumni->wasRecentlyCreated) {
            $alumni->roles()->attach($roleId('alumni'));
        }

        // 5) ALUMNI
        $alumni = User::query()->firstOrCreate(
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
        if ($alumni->wasRecentlyCreated) {
            $alumni->roles()->attach($roleId('alumni'));
        }

        // 6) ALUMNI
        $alumni = User::query()->firstOrCreate(
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
        if ($alumni->wasRecentlyCreated) {
            $alumni->roles()->attach($roleId('alumni'));
        }

        // 7) ALUMNI
        $alumni = User::query()->firstOrCreate(
            ['email' => 'Paslan@rausyanfikr.com'],
            [
                'name' => 'Paslan',
                'password' => Hash::make('password123'),
                'email_verified_at' => $now,
                'phone' => '081200000004',
                'angkatan' => 2023,
                'pekerjaan' => 'Karyawan Swasta',
                'wilayah_id' => 2,
                'level_id' => 4,
                'status' => 'active',
                'tempat_lahir' => 'Luwu Timur',
                'tanggal_lahir' => '1998-08-20',
                'pendidikan_terakhir' => 'S1',
                'kampus' => null,
                'status_pernikahan' => 'Lajang',
                'consent_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
        if ($alumni->wasRecentlyCreated) {
            $alumni->roles()->attach($roleId('alumni'));
        }

        // 8) ALUMNI
        $alumni = User::query()->firstOrCreate(
            ['email' => 'Bambang@rausyanfikr.com'],
            [
                'name' => 'Bambang',
                'password' => Hash::make('password123'),
                'email_verified_at' => $now,
                'phone' => '081200004948',
                'angkatan' => 2023,
                'pekerjaan' => 'Karyawan Swasta',
                'wilayah_id' => 2,
                'level_id' => 4,
                'status' => 'active',
                'tempat_lahir' => 'Bone',
                'tanggal_lahir' => '1997-05-20',
                'pendidikan_terakhir' => 'S1',
                'kampus' => null,
                'status_pernikahan' => 'Lajang',
                'consent_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
        if ($alumni->wasRecentlyCreated) {
            $alumni->roles()->attach($roleId('alumni'));
        }
    }
}
