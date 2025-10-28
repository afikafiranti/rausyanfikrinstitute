<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('tempat_lahir', 100)->nullable()->after('pekerjaan');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('pendidikan_terakhir', 100)->nullable()->after('tanggal_lahir');
            $table->string('kampus', 150)->nullable()->after('pendidikan_terakhir');
            $table->string('status_pernikahan', 30)->nullable()->after('kampus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'tempat_lahir',
                'tanggal_lahir',
                'pendidikan_terakhir',
                'kampus',
                'status_pernikahan',
            ]);
        });
    }
};
