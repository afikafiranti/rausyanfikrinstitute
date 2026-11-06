<?php

namespace Database\Seeders;

use App\Models\KajianReport;
use App\Models\Playlist;
use App\Models\Role;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WorkflowExampleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::query()->pluck('id', 'name');
        foreach ([
            ['contoh.pending@rausyanfikr.test', 'pending', 2, ['alumni']],
            ['contoh.suspended@rausyanfikr.test', 'suspended', 2, ['alumni']],
            ['contoh.tanpa-wilayah@rausyanfikr.test', 'active', null, ['koorda']],
            ['contoh.tanpa-role@rausyanfikr.test', 'active', null, []],
            ['contoh.multi-role@rausyanfikr.test', 'active', 3, ['koorda', 'alumni']],
        ] as [$email, $status, $wilayahId, $roleNames]) {
            $user = User::query()->firstOrCreate(['email' => $email], [
                'name' => 'Contoh '.strtok(substr($email, 7), '@'),
                'password' => Hash::make('password123'),
                'status' => $status,
                'wilayah_id' => $wilayahId,
                'level_id' => 1,
                'email_verified_at' => now(),
                'consent_at' => now(),
            ]);
            if ($user->wasRecentlyCreated) {
                $user->roles()->attach(array_map(fn ($name) => $roles[$name], $roleNames));
            }
        }

        foreach ([
            ['Contoh Materi Level 1', 'PL_CONTOH_LEVEL_1', 1, true, 'CONTOH00001'],
            ['Contoh Materi Nonaktif', 'PL_CONTOH_NONAKTIF', 2, false, 'CONTOH00002'],
        ] as [$title, $playlistId, $levelId, $isActive, $videoId]) {
            $playlist = Playlist::query()->firstOrCreate(['youtube_playlist_id' => $playlistId], [
                'title' => $title,
                'level_id' => $levelId,
                'description' => 'Materi contoh untuk pengembangan.',
                'is_active' => $isActive,
                'order_index' => 1,
            ]);
            Video::query()->firstOrCreate(['youtube_video_id' => $videoId], [
                'playlist_id' => $playlist->id,
                'title' => $title.' Video',
                'duration_sec' => 900,
                'order_index' => 1,
            ]);
        }

        $owner = User::query()->where('email', 'koorda@rausyanfikr.test')->firstOrFail();
        $reviewer = User::query()->where('email', 'arif@rausyanfikr.test')->firstOrFail();
        foreach (['draft', 'submitted', 'approved', 'rejected'] as $status) {
            KajianReport::query()->firstOrCreate(
                ['user_id' => $owner->id, 'judul' => 'Contoh Laporan '.ucfirst($status)],
                [
                    'wilayah_id' => $owner->wilayah_id,
                    'tanggal' => today()->subDays(4)->toDateString(),
                    'tempat' => 'Lokasi contoh',
                    'status' => $status,
                    'reviewed_by' => in_array($status, ['approved', 'rejected'], true) ? $reviewer->id : null,
                    'reviewed_at' => in_array($status, ['approved', 'rejected'], true) ? now() : null,
                    'reject_reason' => $status === 'rejected' ? 'Alasan contoh untuk revisi.' : null,
                ]
            );
        }
    }
}
