<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $t) {
            if (!Schema::hasColumn('users','angkatan')) {
                $t->string('angkatan', 20)->nullable()->after('phone');
            }
            $t->index('angkatan', 'users_angkatan_index');
            $t->index(['wilayah_id','level_id','status'], 'users_wilayah_level_status_index');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->dropIndex('users_angkatan_index');
            $t->dropIndex('users_wilayah_level_status_index');
        });
    }
};
