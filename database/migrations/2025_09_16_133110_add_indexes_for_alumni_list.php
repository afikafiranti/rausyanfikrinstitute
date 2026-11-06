<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->index('created_at', 'idx_users_created_at');
            $t->index('name', 'idx_users_name');   // LIKE prefix & sort
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
            $t->dropIndex('idx_users_created_at');
            $t->dropIndex('idx_users_name');
        });
        Schema::table('wilayah', function (Blueprint $t) { $t->dropIndex('idx_wilayah_name'); });
        Schema::table('levels', function (Blueprint $t) { $t->dropIndex('idx_levels_name');  });
    }
};
