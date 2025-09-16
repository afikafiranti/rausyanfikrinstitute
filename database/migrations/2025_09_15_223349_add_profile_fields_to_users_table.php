<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('photo_url', 255)->nullable()->after('phone');
            $table->unsignedSmallInteger('angkatan')->nullable()->after('photo_url');
            $table->string('pekerjaan', 100)->nullable()->after('angkatan');

            $table->foreignId('wilayah_id')->nullable()
                  ->after('pekerjaan')
                  ->constrained('wilayah')->nullOnDelete();

            $table->foreignId('level_id')->nullable()
                  ->after('wilayah_id')
                  ->constrained('levels')->nullOnDelete();

            $table->enum('status', ['pending', 'active', 'suspended'])
                  ->default('pending')
                  ->after('level_id');

            $table->timestamp('consent_at')->nullable()->after('status');

            $table->index(['wilayah_id', 'level_id', 'status'], 'users_wilayah_level_status_idx');
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_wilayah_level_status_idx');

            $table->dropConstrainedForeignId('level_id');
            $table->dropConstrainedForeignId('wilayah_id');

            $table->dropColumn([
                'phone', 'photo_url', 'angkatan', 'pekerjaan',
                'status', 'consent_at',
            ]);
        });
    }
};
