<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->index(['wilayah_id','level_id','status'], 'idx_users_wilayah_level_status');
            $t->index('angkatan', 'idx_users_angkatan');
            $t->index('created_at', 'idx_users_created_at');
            $t->index('name', 'idx_users_name');   // LIKE prefix & sort
            $t->index('email', 'idx_users_email'); // filter/sort
        });
        Schema::table('wilayah', function (Blueprint $t) {
            $t->index('name', 'idx_wilayah_name'); // sort by wilayah name
        });
        Schema::table('levels', function (Blueprint $t) {
            $t->index('name', 'idx_levels_name');  // sort by level name
        });
    }
    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->dropIndex('idx_users_wilayah_level_status');
            $t->dropIndex('idx_users_angkatan');
            $t->dropIndex('idx_users_created_at');
            $t->dropIndex('idx_users_name');
            $t->dropIndex('idx_users_email');
        });
        Schema::table('wilayah', function (Blueprint $t) { $t->dropIndex('idx_wilayah_name'); });
        Schema::table('levels', function (Blueprint $t) { $t->dropIndex('idx_levels_name');  });
    }
};
