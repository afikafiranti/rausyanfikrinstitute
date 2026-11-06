<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fail before any DDL: resolving duplicate account numbers needs an explicit decision.
        $duplicatePhone = DB::table('users')->select('phone')->whereNotNull('phone')
            ->groupBy('phone')->havingRaw('COUNT(*) > 1')->first();
        if ($duplicatePhone !== null) {
            throw new RuntimeException('Nomor telepon duplikat ditemukan. Perbaiki data develop sebelum migrasi; tidak ada nomor akun yang diubah otomatis.');
        }

        Schema::dropIfExists('video_progress');

        $indexes = collect(DB::select('SHOW INDEX FROM users'))->pluck('Key_name')->unique()->all();
        foreach (['users_wilayah_level_status_index', 'idx_users_wilayah_level_status', 'idx_users_angkatan', 'idx_users_email'] as $index) {
            if (in_array($index, $indexes, true)) {
                Schema::table('users', fn (Blueprint $table) => $table->dropIndex($index));
            }
        }

        if (! in_array('users_phone_unique', $indexes, true)) {
            Schema::table('users', fn (Blueprint $table) => $table->unique('phone'));
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('users', 'users_phone_unique')) {
            Schema::table('users', fn (Blueprint $table) => $table->dropUnique('users_phone_unique'));
        }
    }
};
