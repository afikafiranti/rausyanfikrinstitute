<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kajian_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wilayah_id')->nullable()->constrained('wilayah')->nullOnDelete();
            $table->date('tanggal');
            $table->string('tempat', 150)->nullable();
            $table->string('judul', 180);
            $table->string('pemateri', 150)->nullable();
            $table->unsignedInteger('peserta_l')->default(0);
            $table->unsignedInteger('peserta_p')->default(0);
            $table->unsignedInteger('total_peserta')->default(0);
            $table->unsignedInteger('durasi_menit')->nullable();
            $table->text('catatan')->nullable();
            $table->string('foto_url')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('reject_reason')->nullable();
            $table->timestamps();

            $table->index(['wilayah_id', 'status', 'tanggal'], 'kajian_reports_wilayah_status_tanggal_idx');
            $table->index(['user_id', 'tanggal'], 'kajian_reports_user_tanggal_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kajian_reports');
    }
};
